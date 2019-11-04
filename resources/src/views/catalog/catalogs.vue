<template>
  <GetData
    :callback="fetchFolders"
  >
    <Layout>
      <template #sidebar-title>
        Каталог
      </template>
      <template #sidebar>
        <div>
          <router-link
            to="/catalogs"
            class="sidebar-link"
          >
            Каталоги и товары
          </router-link>
        </div>
        <div>
          <router-link
            to="/catalogs/prices"
            class="sidebar-link"
          >
            <span>
              Группы цен
            </span>
            <span class="sidebar-link_stat">
              {{ stats['price_group'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            to="/catalogs/attributes"
            class="sidebar-link"
          >
            <span>
              Характеристики
            </span>
            <span class="sidebar-link_stat">
              {{ stats['attrs'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            to="/catalogs/currencies"
            class="sidebar-link"
          >
            <span>
              Валюты
            </span>
            <span class="sidebar-link_stat">
              {{ stats['currencies'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            to="/catalogs/trademarks"
            class="sidebar-link"
          >
            <span>
              Торговые марки
            </span>
            <span class="sidebar-link_stat">
              {{ stats['trademark'] }}
            </span>
          </router-link>
        </div>
        <div>
          <b-button @click="exportActive = true">
            Экспорт
          </b-button>
          <b-button @click="importActive = true">
            Импорт
          </b-button>
          <export-catalog-popup
            :is-active="exportActive"
            @submit="exportCatalog"
            @close="exportActive = false"
          />
          <import-catalog-popup
            :is-active="importActive"
            @submit="importCatalog"
            @close="importActive = false"
          />
          <a
            ref="link"
            href="#"
            target="_blank"
            class="download_link"
          >
            Download
          </a>
        </div>
      </template>
      <template #content>
        <b-loading
          :active="isLoading"
        />
        <router-view :refresh="refresh" />
      </template>
    </Layout>
  </GetData>
</template>

<script>
import { CATALOG_MODULE } from '@/store/modules/TYPES'
import { mapState, mapActions } from 'vuex'
import Layout from '@/components/Layout/Layout'
import exportCatalogPopup from '@/components/Popups/ExportCatalogPopup'
import importCatalogPopup from '@/components/Popups/ImportCatalogPopup'
const { IMPORT_CATALOG, CATALOG_STATS, GET_CATALOG_INFO, EXPORT_CATALOG } = CATALOG_MODULE

export default {
  components: { Layout, exportCatalogPopup, importCatalogPopup },
  data () {
    return {
      exportActive: false,
      importActive: false,
      isLoading: false,
      refresh: false
    }
  },
  computed: {
    ...mapState({
      stats: store => store.catalogModule[CATALOG_STATS]
    })
  },
  methods: {
    ...mapActions([GET_CATALOG_INFO, EXPORT_CATALOG, IMPORT_CATALOG]),
    fetchFolders () {
      return this[GET_CATALOG_INFO]()
    },
    importCatalog (file) {
      this.isLoading = true
      const data = new FormData()
      data.append('file', file)
      this[IMPORT_CATALOG](data).then(() => {
        this.$buefy.toast.open('Каталог импортирован')
        this.refresh = !this.refresh
      }).finally(() => {
        this.isLoading = false
      })
      console.log(file)
    },
    exportCatalog (fields) {
      this[EXPORT_CATALOG](fields).then(response => {
        console.log(response.data)
        this.$refs.link.href = `${location.origin}/${response.data}`
        this.$refs.link.click()
      })
    }
  }
}
</script>

<style>
.download_link {
  display: none;
}
</style>
