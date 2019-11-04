<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Склад: {{ name }}
    </template>
    <template slot="content">
      <b-loading :active="isLoading" />
      <GetData :callback="fetchData">
        <h3>Каталоги склада</h3>
        <b-field>
          <b-checkbox-button
            v-for="folder in folders"
            :key="'folder_' + folder.id"
            v-model="activeFolders"
            :native-value="folder.id"
            @input="onInput"
          >
            <b-icon icon="package-variant" />
            <span>{{ folder.name }}</span>
          </b-checkbox-button>
        </b-field>
      </GetData>
    </template>
  </Popup>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { STOCK_MODULE } from '@/store/modules/TYPES'
const { GET_STOCK_CATALOGS, ROOT_FOLDERS, STOCK, UPDATE_STOCK_CATALOGS } = STOCK_MODULE

export default {
  props: {
    isActive: Boolean,
    name: {
      type: String,
      required: true
    },
    id: {
      type: [String, Number],
      required: true
    }
  },
  data () {
    return {
      activeFolders: [],
      cacheActiveFolders: [],
      isLoading: false
    }
  },
  computed: {
    ...mapState({
      folders: state => state.stockModule[ROOT_FOLDERS],
      stock: state => state.stockModule[STOCK]
    })
  },
  methods: {
    ...mapActions([GET_STOCK_CATALOGS, UPDATE_STOCK_CATALOGS]),
    fetchData () {
      return this[GET_STOCK_CATALOGS](this.id).then(response => {
        if (this.stock && this.stock.folders.length) {
          this.activeFolders = this.stock.folders.map(folder => folder.id)
          this.cacheActiveFolders = [...this.activeFolders]
        }
        return response
      })
    },
    onInput () {
      this.isLoading = true
      const data = {
        id: this.id,
        data: {
          ids: this.activeFolders
        }
      }
      this[UPDATE_STOCK_CATALOGS](data).then(response => {
        this.$buefy.toast.open('Данные обновлены')
        this.cacheActiveFolders = [...this.activeFolders]
      }).catch((e) => {
        this.$buefy.toast.open('Данные не обновлены')
        this.activeFolders = [...this.cacheActiveFolders]
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
