<template>
  <GetData :callback="fetch">
    <create-plugin-popup
      v-if="createPlugin"
      :is-active="!!createPlugin"
      :fields="createPluginFields"
      :title="createPlugin.label"
      :type="createPlugin.type"
      @close="createPlugin = null"
    />
    <div>
      <b-dropdown>
        <b-button slot="trigger">
          <b-icon icon="plus" />
        </b-button>
        <b-dropdown-item @click="setCreatePlugin('np')">
          Создать плагин новой почты
        </b-dropdown-item>
        <b-dropdown-item @click="setCreatePlugin('turbo')">
          Создать плагин турбосмс
        </b-dropdown-item>
        <b-dropdown-item @click="setCreatePlugin('roi')">
          Создать плагин роистата
        </b-dropdown-item>
      </b-dropdown>
    </div>
    <div class="is-flex is-multiline">
      <div
        v-for="plugin in plugins"
        :key="plugin.id"
        class="is-4-desktop is-4-tablet is-6-mobile column"
      >
        <plugin-card :plugin="plugin" />
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { PLUGIN_MODULE } from '@/store/modules/TYPES'
import PluginCard from '@/components/Cards/PluginCard'
import CreatePluginPopup from '@/components/popups/CreatePluginPopup'
import plugins from '@/helpers/plugins'
const { PLUGINS, GET_PLUGINS } = PLUGIN_MODULE

export default {
  components: {
    PluginCard,
    CreatePluginPopup
  },
  data () {
    return {
      createPlugin: null,
      info: plugins
    }
  },
  computed: {
    ...mapState({
      plugins: store => store.pluginModule[PLUGINS]
    }),
    createPluginFields () {
      return this.createPlugin ? this.createPlugin.fields : []
    }
  },
  methods: {
    ...mapActions([GET_PLUGINS]),
    fetch () {
      return this[GET_PLUGINS]()
    },
    setCreatePlugin (type) {
      this.createPlugin = {
        ...this.info[type],
        type
      }
    }
  }
}
</script>
