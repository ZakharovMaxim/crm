<template>
  <div class="sortable-list">
    <b-loading :active="isLoading" />
    <div class="imagelist-tools">
      <b-button
        type="is-primary"
        @click="openFileSelect"
      >
        Добавить изображения
      </b-button>
      <input
        ref="fileInput"
        type="file"
        multiple
        @change="fileListChanged"
      >
      <div class="tags">
        <span
          v-for="(file, i) in fileList"
          :key="'tag_' + i"
          class="tag is-primary"
        >
          {{ file.name }}
          <button
            class="delete is-small"
            type="button"
            @click="deleteFile(i)"
          />
        </span>
      </div>
      <div v-if="fileList.length">
        <b-button @click="updloadFiles">
          Отправить
        </b-button>
      </div>
    </div>
    <gallery
      :images="links"
      :index="index"
      @close="index = null"
    />
    <div class="image-list">
      <v-grid-sortable
        :center="false"
        :draggable="true"
        :sortable="true"
        :items="sortedList"
        :cell-width="180"
        :cell-height="280"
        :single-row="true"
        @itemClick="pageListItemClick"
        @dragEnd="pageListSort"
      >
        <template
          slot="cell"
          slot-scope="slotCellProps"
        >
          <div class="image-card editable-card">
            <div class="card-content">
              <div class="card-tools">
                <b-tooltip
                  label="Удалить"
                  position="is-left"
                >
                  <div
                    class="card-tool card-tool--danger"
                    @click="removeImage(slotCellProps.item.id)"
                    @mousedown.stop
                    @touchstart.stop
                  >
                    <b-icon
                      icon="delete"
                    />
                  </div>
                </b-tooltip>
              </div>
              <img
                :src="slotCellProps.item.url"
                :alt="alt"
              >
            </div>
          </div>
        </template>
      </v-grid-sortable>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import VueGallery from 'vue-gallery'
import { IMAGE_MODULE } from '@/store/modules/TYPES'
const { UPLOAD_IMAGES, DESTROY_IMAGE, SORT_IMAGES } = IMAGE_MODULE

const acceptedTypes = ['image/png', 'image/jpeg']
export default {
  components: {
    'gallery': VueGallery
  },
  props: {
    images: {
      type: Array,
      required: true
    },
    parentId: {
      type: [String, Number],
      required: true
    },
    type: {
      type: String,
      default: 'product'
    },
    alt: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      fileList: [],
      isLoading: false,
      isVariation: this.type === 'variation',
      index: null,
      cache: null
    }
  },
  computed: {
    sortedList () {
      return this.images.slice().sort((a, b) => a.pivot.order - b.pivot.order)
    },
    links () {
      return this.sortedList.map(item => item.url)
    }
  },
  methods: {
    ...mapActions([UPLOAD_IMAGES, DESTROY_IMAGE, SORT_IMAGES]),
    pageListItemClick (item) {
      this.index = item.index
    },
    pageListSort (list) {
      const sortList = this.getSortedList(list)
      if (this.cache) {
        if (JSON.stringify(this.cache) === JSON.stringify(sortList)) {
          return
        }
      } else {
        let hasChanged = false
        list.items.forEach(item => {
          if (item.index !== item.sort) hasChanged = true
        })
        if (!hasChanged) {
          return
        }
      }
      this.cache = sortList
      this[SORT_IMAGES]({
        list: sortList,
        parent_id: this.parentId,
        is_variation: this.isVariation
      }).then(() => {
        this.$buefy.toast.open('Порядок сохранен')
      })
    },
    getSortedList (list) {
      return list.items.map(item => {
        return {
          id: item.item.id,
          order: item.sort + 1
        }
      })
    },
    openFileSelect () {
      this.$refs.fileInput.click()
    },
    fileListChanged (e) {
      this.fileList = Array.prototype.filter.call(e.target.files, (item) => acceptedTypes.includes(item.type))
    },
    deleteFile (index) {
      this.fileList = this.fileList.filter((item, i) => i !== index)
    },
    updloadFiles () {
      const { isVariation } = this
      const data = new FormData()
      this.fileList.forEach(file => {
        data.append('photos[]', file, file.name)
      })
      const lastOrder = this.images.reduce((acc, item) => {
        return item.pivot.order > acc ? item.pivot.order : acc
      }, 0)
      data.append('last_order', lastOrder + 1)
      data.append('parent_id', this.parentId)
      if (isVariation) data.append('is_variation', true)
      this.isLoading = true
      this[UPLOAD_IMAGES](data).then(() => {
        this.$buefy.toast.open('Изображения добавлены')
        this.cache = null
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.fileList = []
        this.isLoading = false
      })
    },
    removeImage (id) {
      this.$buefy.dialog.confirm({
        title: 'Удаление изображение',
        message: 'Вы действительно хотите удалить изображение? Это действие нельзя отменить',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.removeImageConfirmed(id)
      })
    },
    removeImageConfirmed (id) {
      const isVariation = this.type === 'variation'
      this.isLoading = true
      this[DESTROY_IMAGE]({
        id,
        isVariation
      }).then(() => {
        this.cache = null
        this.$buefy.toast.open('Изображение удалено')
      }).catch(e => {
        this.$buefy.toast.open('Изображение не удалено')
        console.log(e)
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style scoped lang="scss">
.image-list {
  width: 100%;
  overflow-x: scroll;
  overflow-y: hidden;
}
.image-card {
  img {
    max-width: 100%;
  }
}
input[type="file"] {
  display: none;
}
.tags {
  margin: 10px 0;
}
</style>
