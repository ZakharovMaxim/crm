<template>
  <div
    ref="self"
    :class="classes"
    :style="styles"
    @mousedown="mousedown"
    @touchstart.stop="mousedown"
  >
    <slot />
  </div>
</template>

<script>
const CLICK_PIXEL_DISTANCE = 4

export default {
  name: 'VGridSortableItem',

  props: {
    index: { type: Number, required: true },
    sort: { type: Number, required: true },
    cellWidth: { type: Number, required: true },
    cellHeight: { type: Number, required: true },
    rowCount: { type: Number, required: true },
    rowShift: { type: Number, default: 0 },
    draggable: { type: Boolean, required: true },
    dragDelay: { type: Number, default: 0 }
  },

  data () {
    return {
      animate: true,
      dragging: false,

      shiftStartX: 0,
      shiftStartY: 0,

      mouseMoveStartX: 0,
      mouseMoveStartY: 0,

      shiftX: 0,
      shiftY: 0,

      timer: null,

      zIndex: 1
    }
  },

  computed: {
    classes () {
      let { animate, dragging } = this

      return [
        'v-grid-sortable-item-wrapper',
        {
          'v-grid-sortable-item-animate': animate,
          'v-grid-sortable-item-dragging': dragging
        }
      ]
    },

    styles () {
      let { zIndex, cellWidth, left } = this

      return {
        zIndex,
        width: cellWidth + 'px',
        transform: `translate3d(${left}px, 0, 0)`
      }
    },

    left () {
      return this.dragging
        ? this.shiftX
        : this.rowShift + (this.sort % this.rowCount) * this.cellWidth
    },

    top () {
      return this.dragging
        ? this.shiftY
        : Math.floor(this.sort / this.rowCount) * this.cellHeight
    }
  },

  mounted () {
    this.$refs.self.addEventListener('transitionend', event => {
      if (!this.dragging) {
        this.zIndex = 1
      }
    }, false)
  },

  methods: {
    wrapEvent (event) {
      let { index, sort } = this
      return { event, index, sort }
    },

    dragStart (event) {
      let e = event.touches ? event.touches[0] : event

      this.zIndex = 2

      this.shiftX = this.shiftStartX = this.left
      this.shiftY = this.shiftStartY = this.top

      this.mouseMoveStartX = e.pageX
      this.mouseMoveStartY = e.pageY

      this.animate = false
      this.dragging = false

      document.addEventListener('mousemove', this.documentMouseMove)
      document.addEventListener('touchmove', this.documentMouseMove)

      this.$emit('itemDragStart', this.wrapEvent(event))
    },

    drag (event) {
      let e = event.touches ? event.touches[0] : event

      let distanceX = e.pageX - this.mouseMoveStartX
      let distanceY = e.pageY - this.mouseMoveStartY

      this.shiftX = distanceX + this.shiftStartX
      this.shiftY = distanceY + this.shiftStartY

      let gridX = Math.round(this.shiftX / this.cellWidth)
      let gridY = Math.round(this.shiftY / this.cellHeight)

      gridX = Math.min(gridX, this.rowCount - 1)
      gridY = Math.max(gridY, 0)

      let gridPosition = gridX + gridY * this.rowCount

      const $event = {
        event,
        distanceX,
        distanceY,
        positionX: this.shiftX,
        positionY: this.shiftY,
        index: this.index,
        gridX,
        gridY,
        gridPosition
      }

      this.$emit('itemDrag', $event)
    },

    mousedown (event) {
      event.preventDefault()
      if (this.draggable) {
        this.timer = setTimeout(() => this.dragStart(event), this.dragDelay)

        document.addEventListener('mouseup', this.documentMouseUp)
        document.addEventListener('touchend', this.documentMouseUp)
      }
    },

    documentMouseMove (event) {
      if (this.draggable) {
        event.preventDefault()
        this.dragging = true
        this.drag(event)
      }
    },

    documentMouseUp (event) {
      event.preventDefault()
      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }

      let dx = this.shiftStartX - this.shiftX
      let dy = this.shiftStartY - this.shiftY

      let distance = Math.sqrt(dx * dx + dy * dy)

      this.animate = true
      this.mouseMoveStartX = 0
      this.mouseMoveStartY = 0
      this.shiftStartX = 0
      this.shiftStartY = 0

      document.removeEventListener('mousemove', this.documentMouseMove)
      document.removeEventListener('touchmove', this.documentMouseMove)

      document.removeEventListener('mouseup', this.documentMouseUp)
      document.removeEventListener('touchend', this.documentMouseUp)

      let $event = this.wrapEvent(event)
      if (this.dragging) {
        this.$emit('itemDragend', $event)
      } else if (distance < CLICK_PIXEL_DISTANCE) {
        this.$emit('itemClick', $event)
      }

      this.dragging = false
    }
  }
}
</script>

<style>
  .v-grid-sortable-item-wrapper {
    display: inline-flex;
    position: absolute;
    box-sizing: border-box;

    left: 0;
    top: 0;

    user-select: none;
    transform: translate3d(0px, 0px, 0px);

    z-index: 1;
  }

  .v-grid-sortable-item-animate {
    transition: transform 800ms ease;
  }
</style>
