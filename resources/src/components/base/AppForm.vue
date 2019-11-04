<template>
  <b-field
    :type="config.form.getBuefyType('form')"
    :message="config.form.errors.get('form')"
  >
    <div class="is-flex is-multiline">
      <div
        v-for="(row, index) in config.rows"
        :key="`form_row_${index}`"
        class="is-flex is-multiline column"
        :class="desktopClass"
      >
        <div
          v-for="(field, key) in row"
          :key="`input_field_${key}`"
          class="column"
          :class="field.className || defaultInputClassName"
        >
          <b-field
            class="w-100"
            :label="field.placeholder || ''"
            :type="config.form.getBuefyType(key)"
            :message="config.form.errors.get(key)"
          >
            <b-input
              v-if="!field.type"
              :value="config.form[key]"
              :placeholder="field.placeholder"
              @input="v => handleInput(v, key)"
            />
            <b-select
              v-else
              :value="config.form[key]"
              :placeholder="field.placeholder"
              @input="v => handleInput(v, key)"
            >
              <option
                v-for="option in options(key)"
                :key="`form_option_${option.id}`"
                :value="option.id"
              >
                {{ displayName(key, option) }}
              </option>
            </b-select>
          </b-field>
        </div>
      </div>
    </div>
  </b-field>
</template>

<script>

export default {
  props: {
    config: {
      type: Object,
      required: true
    },
    data: {
      type: Object,
      default: () => {}
    }
  },
  computed: {
    desktopClass () {
      return `is-${Math.ceil(12 / this.config.rows.length)}`
    },
    defaultInputClassName () {
      return `is-12-mobile is-12-tablet is-12`
    }
  },
  methods: {
    handleInput (v, type) {
      this.config.form.errors.remove(type)
      this.config.form.errors.remove('form')
      this.config.form[type] = v
    },
    options (key) {
      const data = this['data'][key]
      if (!data) return []
      if (Array.isArray(data)) {
        return data
      }

      return data.items || []
    },
    displayName (key, item) {
      const data = this['data'][key]
      if (!data) return ''
      return data.displayName ? data.displayName(item) : item.name
    }
  }
}
</script>
