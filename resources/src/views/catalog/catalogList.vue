<template>
  <div
    v-title="title"
    class="h-100"
  >
    <b-loading
      :active.sync="isLoading"
    />
    <div class="search-container">
      <b-input
        v-model="localSearch"
        placeholder="Введите название каталога или товара"
      />
    </div>
    <GetData
      :callback="fetchData"
      :update="queryChanged"
    >
      <template #default="slotProps">
        <breadcrumbs
          :breadcrumbs="breadcrumbs"
        />
        <b-dropdown aria-role="list">
          <button
            slot="trigger"
            class="button is-primary action-button"
          >
            <b-icon
              icon="plus"
              size="is-large"
              title="Создать позицию"
              class="is-action"
            />
          </button>
          <b-dropdown-item
            aria-role="listitem"
            @click="isCategoryCreateActive = true"
          >
            Создать каталог
          </b-dropdown-item>
          <b-dropdown-item
            v-if="!isRootDirectory"
            aria-role="listitem"
            @click="isProductCreateActive = true"
          >
            Создать товар
          </b-dropdown-item>
          <b-dropdown-item
            v-if="!isRootDirectory"
            aria-role="listitem"
            @click="isVariationProductCreateActive = true"
          >
            Создать вариативный товар
          </b-dropdown-item>
        </b-dropdown>
        <CreateCategoryPopup
          :is-active="isCategoryCreateActive"
          @close="isCategoryCreateActive = false"
        />
        <CreateProductPopup
          :is-active="isProductCreateActive"
          :root-dir="breadcrumbs[1]"
          @close="isProductCreateActive = false"
        />
        <CreateVariationProductPopup
          :is-active="isVariationProductCreateActive"
          :root-dir="breadcrumbs[1]"
          @close="isVariationProductCreateActive = false"
        />
        <div class="is-flex is-multiline">
          <div
            v-for="folder in filteredFolders"
            :key="'folder' + folder.id"
            class="column is-4 is-4-tablet is-12-mobile"
          >
            <Folder
              :id="folder.id"
              :name="folder.name"
              :parent-id="folder.parent_id"
              @delete="deleteFolder"
              @update="setFolderToUpdate"
              @click="setFolder(folder.id)"
            />
          </div>
          <div
            v-for="product in products"
            :key="'product_' + product.id"
            class="column is-12 is-12-mobile is-12-tablet"
          >
            <router-link
              :to="'/catalogs/product/' + product.id"
              class="card editable-card"
            >
              <ProductCard
                :product="product"
                @delete="deleteProduct"
              />
            </router-link>
          </div>
        </div>
        <div
          v-if="!products.length && !filteredFolders.length"
          class="tac notice"
        >
          {{ emptyDirectoryMessage }}
        </div>
        <b-pagination
          v-if="+slotProps.data.total"
          :total="+slotProps.data.total"
          :current="localPage"
          order="is-centered"
          size="is-small"
          rounded
          :per-page="perPage"
          aria-next-label="Следующая страница"
          aria-previous-label="Предыдущая страница"
          aria-page-label="Страница"
          aria-current-label="Текущая страница"
          @change="changePage"
        />
        <UpdateCategoryPopup
          v-if="folderToUpdate"
          :id="folderToUpdate.id"
          :parent-id="folderToUpdate.parent_id"
          :is-active="!!folderToUpdate"
          :name="folderToUpdate.name"
          @close="folderToUpdate = null"
        />
      </template>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PRODUCT_MODULE, FOLDER_MODULE } from '@/store/modules/TYPES'
import TreeService from '@/helpers/Tree'
import Folder from '@/components/Cards/Folder'
import ProductCard from '@/components/Cards/ProductCard'
import UpdateCategoryPopup from '@/components/popups/UpdateCategoryPopup'
import CreateCategoryPopup from '@/components/popups/CreateCategoryPopup'
import CreateProductPopup from '@/components/popups/CreateProductPopup'
import CreateVariationProductPopup from '@/components/popups/CreateVariationProductPopup'
import Breadcrumbs from '@/components/layout/Breadcrumbs'
const { GET_PRODUCTS, PRODUCTS, DESTROY_PRODUCT } = PRODUCT_MODULE
const { DESTROY_FOLDER, UPDATE_FOLDER, FOLDERS } = FOLDER_MODULE

export default {
  components: { Folder, UpdateCategoryPopup, CreateCategoryPopup, CreateProductPopup, ProductCard, CreateVariationProductPopup, Breadcrumbs },
  props: {
    parentId: {
      type: [String, Number],
      default: ''
    },
    page: {
      type: String,
      default: ''
    },
    search: {
      type: String,
      default: ''
    },
    refresh: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      isLoading: false,
      folderToUpdate: null,
      isCategoryCreateActive: false,
      isProductCreateActive: false,
      isVariationProductCreateActive: false,
      queryChanged: false,
      localSearch: this.search || '',
      localPage: +this.page || 1,
      perPage: 20
    }
  },
  computed: {
    ...mapState({
      products: state => state.catalogModule[PRODUCTS],
      folders: state => state.catalogModule[FOLDERS]
    }),
    isRootDirectory () {
      return !+this.$route.query.parent_id
    },
    emptyDirectoryMessage () {
      return this.isRootDirectory ? 'Нет каталогов' : 'Нет товаров'
    },
    breadcrumbs () {
      return TreeService.getTreePath(this.parentId, this.folders)
    },
    title () {
      console.log(this.breadcrumbs)
      const brd = this.breadcrumbs
      let res = 'Каталог'
      if (brd && brd.length) {
        res += `: ${brd[brd.length - 1].name}`
      }
      return res
    },
    filteredFolders () {
      if (!this.folders) return []
      const currentFolder = this.$route.query.parent_id || null
      /* eslint eqeqeq: [0, "allow-null"] */
      return this.folders.filter(folder => {
        return (folder.parent_id == currentFolder) &&
                (folder.name.indexOf(this.search) !== -1 || !this.search) &&
                this.localPage < 2
      })
    }
  },
  watch: {
    '$route': function () {
      this.localPage = +this.$route.query.page || 1
      this.queryChanged = !this.queryChanged
    },
    'refresh': function () {
      this.localPage = +this.$route.query.page || 1
      this.queryChanged = !this.queryChanged
    },
    'localSearch': function (v) {
      clearTimeout(this.searchTimer)
      this.searchTimer = setTimeout(() => {
        const query = { ...this.$route.query, search: v }
        if (!v) delete query.search
        this.$router.push({
          query
        })
      }, 500)
    }
  },
  methods: {
    ...mapActions([GET_PRODUCTS, DESTROY_FOLDER, UPDATE_FOLDER, DESTROY_PRODUCT]),
    changePage (page) {
      this.localPage = +page
      this.$router.push({
        query: {
          ...this.$route.query,
          page: page + ''
        }
      })
    },
    fetchData () {
      return this[GET_PRODUCTS]({
        parent_id: this.parentId || null,
        limit: this.perPage,
        page: this.page || 1,
        search: this.search || ''
      })
    },
    deleteFolder (id) {
      this.isLoading = true
      this[DESTROY_FOLDER](id).then(() => {
        this.$buefy.toast.open('Каталог удален')
      }).finally(() => {
        this.isLoading = false
      })
    },
    deleteProduct (id) {
      this.isLoading = true
      this[DESTROY_PRODUCT](id).then(() => {
        this.$buefy.toast.open('Продукт удален')
      }).finally(() => {
        this.isLoading = false
      })
    },
    setFolderToUpdate (folder) {
      this.folderToUpdate = folder
    },
    updateFolder (payload) {
      this.isLoading = true
      this[UPDATE_FOLDER](payload).then(() => {
        this.$buefy.toast.open('Каталог обновлен')
      }).finally(() => {
        this.isLoading = false
      })
    },
    setFolder (id) {
      this.$router.push({
        query: {
          parent_id: '' + id
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .search-container {
    margin: 10px 0;
  }
  .breadcrumbs {
    margin-bottom: 10px;
    ul {
      display: flex;
      li {
        &:not(:last-child):after {
          content: '/';
          margin: 0 5px;
        }
      }
    }
  }
  .action-button {
    overflow: hidden;
  }
</style>
