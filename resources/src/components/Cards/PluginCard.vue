<template>
  <div
    class="plugin"
  >
    <router-link :to="`/plugins/${plugin.id}`">
      <div class="plugin_name">
        {{ plugin.name + `#${plugin.id}` }}
      </div>
      <div class="plugin_image">
        <img
          :src="info[plugin.type]['icon']"
          :alt="plugin.name + `#${plugin.id}`"
        >
      </div>
    </router-link>
    <div class="plugin_description">
      {{ info[plugin.type]['description'] }}
    </div>
    <div class="plugin_tools">
      <div class="plugin_enable">
        <plugin-state
          :id="plugin.id"
          :is-active="!!plugin.enabled"
        />
      </div>
      <div class="plugin_links">
        <router-link :to="`/plugins/${plugin.id}`">
          <b-button size="is-small">
            <b-icon
              size="is-small"
              icon="settings"
            />
          </b-button>
        </router-link>
        <delete-plugin :id="plugin.id" />
      </div>
    </div>
  </div>
</template>

<script>
import DeletePlugin from '@/components/DeletePlugin'
import PluginState from '@/components/PluginState'
import plugins from '@/helpers/plugins'

export default {
  components: { DeletePlugin, PluginState },
  props: {
    plugin: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      info: plugins
    }
  }
}
</script>

<style scoped lang="scss">
  .plugin {
    padding: 15px;
    border: 1px solid #ccc;
    transition: all 200ms;
    border-radius: 5px;
    &:hover {
      border-color: #008fe2;
    }
    &_name {
      font-weight: bold;
      color: #333;
    }
    &_image {
      img {
        width: 100%;
      }
    }
    &_description {
      font-size: 12px;
    }
    &_tools {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    &_links {
      display: flex;
    }
  }
</style>
