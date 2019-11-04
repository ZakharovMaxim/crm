<template>
  <GetData :callback="fetch">
    <b-loading :active="isLoading" />
    <div v-if="plugin && info && form">
      <delete-plugin
        :id="plugin.id"
        is-redirect
      />
      <plugin-state
        :id="plugin.id"
        :is-active="!!plugin.enabled"
      />
      <div class="plugin_info">
        <div class="plugin_name">
          Плагин: {{ info.label }}
        </div>
        <div class="plugin_image">
          <img
            :src="info.icon"
            alt=""
          >
        </div>
      </div>
      <b-field
        v-for="(field) in info.fields"
        :key="field.key"
        :label="field.label"
        :type="form.getBuefyType(field.key)"
        :message="form.errors.get(field.key)"
      >
        <b-input
          :placeholder="field.label"
          :value="form[field.key]"
          @input="e => handleInput(e, field.key)"
        />
      </b-field>
      <b-button @click="submit">
        Сохранить
      </b-button>
    </div>
  </GetData>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { PLUGIN_MODULE } from '@/store/modules/TYPES'
import { getPlugin } from '@/helpers/plugins'
import validations from '@/validations/store-plugin'
import DeletePlugin from '@/components/DeletePlugin'
import PluginState from '@/components/PluginState'
const { PLUGIN, GET_PLUGIN, UPDATE_PLUGIN } = PLUGIN_MODULE

export default {
  components: { DeletePlugin, PluginState },
  data () {
    return {
      info: null,
      form: {},
      isLoading: false
    }
  },
  computed: {
    ...mapState({
      plugin: store => store.pluginModule[PLUGIN]
    })
  },
  methods: {
    ...mapActions([GET_PLUGIN, UPDATE_PLUGIN]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const settings = []
      const data = this.form.data()
      for (let key in data) {
        settings.push({
          name: key,
          value: data[key]
        })
      }
      this[UPDATE_PLUGIN]({
        id: this.plugin.id,
        data: {
          settings
        }
      }).then(() => {
        this.$buefy.toast.open('Плагин обновлен')
      }).catch(() => {
        this.$buefy.toast.open('Плагин не обновлен')
      }).finally(() => {
        this.isLoading = false
      })
    },
    fetch () {
      return this[GET_PLUGIN](this.$route.params.id).then(() => {
        this.info = getPlugin(this.plugin.type)
        this.form = validations(this.info.fields)
        const settings = this.plugin.settings.reduce((acc, setting) => {
          acc[setting.name] = setting.value
          return acc
        }, {})
        console.log(settings)
        this.form.setData(settings)
      })
    }
  }
}
</script>
