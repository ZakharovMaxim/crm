<template>
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
    <div>
      <div class="filters-content">
        <div class="is-flex">
          <div class="column is-7">
            <div class="filter-group">
              <div class="block">
                <b-field label="Категории доходов">
                  <b-checkbox
                    v-for="category in incomeCategories"
                    :key="'income_' + category.id"
                    v-model="incomeCategoriesCheckbox"
                    :native-value="category.id"
                    :disabled="isDisabled('incomeCategories', category.id)"
                    @input="optionChanged"
                  >
                    {{ category.name }}
                  </b-checkbox>
                </b-field>
              </div>
            </div>
            <div class="filter-group">
              <div class="block">
                <b-field label="Категории расходов">
                  <b-checkbox
                    v-for="category in outcomeCategories"
                    :key="'outcome_' + category.id"
                    v-model="outcomeCategoriesCheckbox"
                    :native-value="category.id"
                    :disabled="isDisabled('outcomeCategories', category.id)"
                    @input="optionChanged"
                  >
                    {{ category.name }}
                  </b-checkbox>
                </b-field>
              </div>
            </div>
            <div class="filter-group">
              <b-field label="Тип платежа">
                <div class="is-flex">
                  <b-checkbox
                    v-model="typesCheckbox"
                    :native-value="1"
                    :disabled="isDisabled('types', 1)"
                    @input="optionChanged"
                  >
                    Входящий
                  </b-checkbox>
                  <b-checkbox
                    v-model="typesCheckbox"
                    :native-value="2"
                    :disabled="isDisabled('types', 2)"
                    @input="optionChanged"
                  >
                    Исходящий
                  </b-checkbox>
                </div>
              </b-field>
            </div>
            <div class="filter-group">
              <b-collapse
                :open="true"
                aria-id="contentIdForA11y3"
              >
                <button
                  slot="trigger"
                  class="button is-success"
                  aria-controls="contentIdForA11y3"
                >
                  Магазины
                </button>
                <div>
                  <div class="block">
                    <b-field>
                      <b-checkbox
                        v-for="category in shops"
                        :key="'shops_' + category.id"
                        v-model="shopsCheckbox"
                        :native-value="category.id"
                        :disabled="isDisabled('shops', category.id)"
                        @input="optionChanged"
                      >
                        {{ category.name }}
                      </b-checkbox>
                    </b-field>
                  </div>
                </div>
              </b-collapse>
            </div>
            <div class="filter-group">
              <b-collapse
                :open="true"
                aria-id="contentIdForA11y4"
              >
                <button
                  slot="trigger"
                  class="button is-success"
                  aria-controls="contentIdForA11y4"
                >
                  Счета
                </button>
                <div>
                  <div class="block">
                    <b-field>
                      <b-checkbox
                        v-for="category in bills"
                        :key="'bill_' + category.id"
                        v-model="billsCheckbox"
                        :native-value="category.id"
                        :disabled="isDisabled('bills', category.id)"
                        @input="optionChanged"
                      >
                        {{ category.name }}
                      </b-checkbox>
                    </b-field>
                  </div>
                </div>
              </b-collapse>
            </div>
          </div>
          <div class="column is-5">
            <b-field label="Сортировать по">
              <b-select
                v-model="sortField"
                @input="optionChanged"
              >
                <option
                  v-for="(field, index) in sortFields"
                  :key="'field_' + index"
                  :value="index"
                >
                  {{ field.label }}
                </option>
              </b-select>
            </b-field>
            <b-field label="Дата">
              <b-daterange @input="takeDate" />
            </b-field>
          </div>
        </div>
      </div>
    </div>
  </b-collapse>
</template>

<script>
import { dbFormat } from '@/helpers/DateFormat'
const sort = [{
  label: 'Сортировать по дате(сначала новые)',
  field: 'date',
  direction: 'DESC'
},
{
  label: 'Сортировать по дате(сначала старые)',
  field: 'date',
  direction: 'ASC'
},
{
  label: 'Сортировать по номеру(по возрастанию)',
  field: 'id',
  direction: 'DESC'
},
{
  label: 'Сортировать по номеру(по убыванию)',
  field: 'id',
  direction: 'ASC'
}]
export default {
  props: {
    outcomeCategories: {
      type: Array,
      required: true
    },
    incomeCategories: {
      type: Array,
      required: true
    },
    bills: {
      type: Array,
      required: true
    },
    shops: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      sortFields: sort,
      sortField: 0,
      outcomeCategoriesCheckbox: [],
      incomeCategoriesCheckbox: [],
      typesCheckbox: [1, 2],
      shopsCheckbox: [],
      billsCheckbox: [],
      date: {}
    }
  },
  created () {
    this.outcomeCategoriesCheckbox = this.outcomeCategories.map(cat => cat.id)
    this.incomeCategoriesCheckbox = this.incomeCategories.map(cat => cat.id)
    this.shopsCheckbox = this.shops.map(shop => shop.id)
    this.billsCheckbox = this.bills.map(bill => bill.id)
  },
  methods: {
    optionChanged () {
      const options = {
        orderBy: this.sortFields[this.sortField].field,
        direction: this.sortFields[this.sortField].direction,
        types: this.typesCheckbox,
        categories: [...this.outcomeCategoriesCheckbox, ...this.incomeCategoriesCheckbox],
        shops: this.shopsCheckbox,
        bills: this.billsCheckbox
      }
      if (this.date.from) {
        options.date_from = dbFormat(this.date.from)
      }
      if (this.date.to) {
        options.date_to = dbFormat(this.date.to)
      }
      this.$emit('options-changed', options)
    },
    isDisabled (type, id) {
      const realType = type + 'Checkbox'
      if (!this[realType]) return true
      if (this[realType].length < 2 && this[realType].includes(id)) return true
    },
    takeDate (value) {
      this.date = value
      this.optionChanged()
    }
  }
}
</script>

<style lang="scss" scoped>
.filters-content {
  margin: 10px 0;
}
.block {
  margin: 5px 0;
}
.filter-group {
  padding: 10px;
  border: 1px solid #eee;
  transition: all 200ms;
  &:hover {
    border-color: #666;
  }
}
</style>
