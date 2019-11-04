<template>
  <div
    class="v-grid-sortable"
    :style="style"
  >
    <grid-sortable-item
      v-for="listItem in list"
      :key="listItem.index"
      :index="listItem.index"
      :sort="listItem.sort"
      :draggable="draggable"
      :drag-delay="dragDelay"
      :row-count="rowCount"
      :cell-width="cellWidth"
      :cell-height="cellHeight"
      :window-width="windowWidth"
      :row-shift="rowShift"
      @itemDragStart="onDragStart"
      @itemDragend="onDragEnd"
      @itemDrag="onDrag"
      @itemClick="itemClick"
    >
      <slot
        name="cell"
        :item="listItem.item"
        :index="listItem.index"
        :sort="listItem.sort"
        :remove="() => { removeItem(listItem) }"
      />
    </grid-sortable-item>
  </div>
</template>
<script>
import WindowSize from './mixins/WindowSize.js'
import GridSortableItem from './GridItem.vue'

export default {
  name: 'VGridSortable',

  components: { GridSortableItem },

  mixins: [WindowSize],

  props: {
    items: { type: Array, default: () => [] },
    gridWidth: { type: Number, default: -1 },
    cellWidth: { type: Number, default: 100 },
    cellHeight: { type: Number, default: 80 },
    draggable: { type: Boolean, default: false },
    dragDelay: { type: Number, default: 0 },
    sortable: { type: Boolean, default: false },
    center: { type: Boolean, default: false },
    singleRow: { type: Boolean, default: false }
  },

  data () {
    return {
      list: []
    }
  },

  computed: {
    gridResponsiveWidth () {
      return this.gridWidth < 0 ? this.windowWidth : Math.min(this.windowWidth, this.gridWidth)
    },

    height () {
      return this.singleRow ? this.cellHeight : Math.ceil(this.items.length / this.rowCount) * this.cellHeight
    },

    style () {
      return { height: this.height + 'px' }
    },

    rowCount () {
      return this.singleRow ? this.items.length : Math.floor(this.gridResponsiveWidth / this.cellWidth)
    },

    rowShift () {
      if (this.center) {
        let contentWidth = this.items.length * this.cellWidth
        let rowShift = contentWidth < this.gridResponsiveWidth
          ? (this.gridResponsiveWidth - contentWidth) / 2
          : (this.gridResponsiveWidth % this.cellWidth) / 2

        return Math.floor(rowShift)
      }

      return 0
    }
  },

  watch: {
    items: {
      handler: function (nextItems = []) {
        const sortedGridIds = this.list.sort((a, b) => a.sort - b.sort).map(item => item.item.id).join(',')
        const sortedNextIds = nextItems.map(item => item.id).join(',')
        if (sortedGridIds !== sortedNextIds) {
          const newList = nextItems.map((item, index) => {
            return { item, index: index, sort: index }
          })
          this.list = newList
        }
      },

      immediate: true
    }
  },

  methods: {
    /* Returns merged event object */
    wrapEvent (other = {}) {
      return { datetime: Date.now(), items: this.getListClone(), ...other }
    },

    /* Returns sorted clone of "list" array */
    getListClone () {
      return this.list.slice(0).sort((a, b) => { return a.sort - b.sort })
      //  .map(v => {
      //    return { ...v.item }
      //  })
    },

    removeItem ({ index }) {
      let removeItem = this.list.find(item => item.index === index)
      let removeItemSort = removeItem.sort

      this.list = this.list
        .filter(item => item.index !== index)
        .map(item => {
          let sort = item.sort > removeItemSort ? (item.sort - 1) : item.sort

          return { ...item, sort }
        })

      this.$emit('remove', this.wrapEvent({ index }))
    },

    onDragStart (event) {
      this.$emit('dragStart', this.wrapEvent(event))
    },

    onDragEnd (event) {
      this.$emit('dragEnd', this.wrapEvent(event))
    },

    itemClick (event) {
      this.$emit('itemClick', this.wrapEvent(event))
    },

    onDrag (event) {
      if (this.sortable) {
        this.sortList(event.index, event.gridPosition)
      }

      this.$emit('drag', this.wrapEvent({ event }))
    },

    sortList (itemIndex, gridPosition) {
      let targetItem = this.list.find(item => item.index === itemIndex)
      let targetItemSort = targetItem.sort

      /*
          Normalizing new grid position
        */
      gridPosition = Math.max(gridPosition, 0)
      /*
          If you remove this line you can drag items to positions that
          are further than items array length
        */
      gridPosition = Math.min(gridPosition, this.list.length - 1)

      if (targetItemSort !== gridPosition) {
        this.list = this.list.map(item => {
          if (item.index === targetItem.index) {
            return { ...item, sort: gridPosition }
          }

          const { sort } = item

          if (targetItemSort > gridPosition) {
            if (sort <= targetItemSort && sort >= gridPosition) {
              return { ...item, sort: sort + 1 }
            }
          }

          if (targetItemSort < gridPosition) {
            if (sort >= targetItemSort && sort <= gridPosition) {
              return { ...item, sort: sort - 1 }
            }
          }

          return item
        })

        this.$emit('sort', this.wrapEvent())
      }
    }
  }
}
</script>

<style>
  .v-grid-sortable {
    display: flex;
    position: relative;
    width: 100%;
  }
</style>
