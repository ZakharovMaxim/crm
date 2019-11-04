<template>
  <div class="filter">
    <div class="filter-params">
      <table>
        <tr
          v-for="(param, index) in filter.getParams()"
          :key="'param' + index"
        >
          <td>{{ param.label }}</td>
          <td>{{ param.values }}</td>
        </tr>
      </table>
    </div>
    <div class="filter-content">
      <b-collapse
        :open="false"
        aria-id="contentIdForA11y1"
      >
        <button
          slot="trigger"
          class="button is-primary"
          aria-controls="contentIdForA11y1"
        >
          Фильтр
        </button>
        <b-field
          v-for="(container, index) in filteredRows"
          :key="'item_container' + index"
          :label="container.label"
        >
          <span v-if="container.type === 'date'">
            <b-daterange
              :date-from="container.data.from"
              :date-to="container.data.to"
              @input="v => container.data = v"
            />
          </span>
          <span v-else-if="container.type === 'list'">
            <b-checkbox
              v-for="(item, i) in container.items"
              :key="'item' + i"
              v-model="container.data"
              :native-value="item.id"
            >
              {{ item[container.displayName] }}
            </b-checkbox>
          </span>
          <span v-else-if="container.type === 'single'">
            <b-select v-model="container.data">
              <option
                v-for="(item, i) in container.items"
                :key="'item' + i"
                :value="item.id"
              >
                {{ item[container.displayName] }}
              </option>
            </b-select>
          </span>
          <span v-else-if="container.type === 'removable'">
            <span>
              {{ `${container.items.name}${container.items.additional_info ? ('(' + container.items.additional_info + ')') : ''}` }}
            </span>
            <span @click="container.data = null">
              <b-icon icon="close" />
            </span>
          </span>
        </b-field>
        <b-button @click="apply">
          Применить
        </b-button>
      </b-collapse>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    filter: {
      type: Object,
      required: true
    }
  },
  computed: {
    filteredRows () {
      let filtered = { ...this.filter.rows }
      for (let key in filtered) {
        if (filtered[key].type === 'removable' && !filtered[key].data) {
          delete filtered[key]
        }
      }
      return filtered
    }
  },
  methods: {
    apply () {
      this.filter.apply()
      this.$emit('apply')
    }
  }
}
</script>

<style>

</style>
