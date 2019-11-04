<template>
  <GetData :callback="fetchData">
    <div v-title="'Валюты'">
      <button
        slot="trigger"
        class="button is-primary"
        @click="isPopupActive = true"
      >
        <b-icon
          icon="plus"
          size="is-large"
          title="Создать валюту"
          class="is-action"
        />
      </button>
      <currency-popup
        v-if="isPopupActive || !!editableCurrency"
        :is-active="isPopupActive || !!editableCurrency"
        :currency="editableCurrency"
        @close="close"
      />
      <div
        v-if="currencies.length"
        class="currencies-list"
      >
        <div
          v-for="currency in currencies"
          :key="'currency_' + currency.id"
          class="card editable-card"
        >
          <div
            class="card-content"
            @click="edit(currency)"
          >
            {{ currency.name }}({{ currency.rate }})
            <div class="card-tools">
              <b-tooltip
                label="Изменить валюту"
                position="is-top"
              >
                <div
                  class="card-tool"
                >
                  <b-icon
                    icon="pencil"
                  />
                </div>
              </b-tooltip>
              <b-tooltip
                label="Удалить валюту"
                position="is-top"
              >
                <div
                  class="card-tool card-tool--danger"
                  @click.stop="confirmDelete(currency)"
                >
                  <b-icon
                    icon="delete"
                  />
                </div>
              </b-tooltip>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="!currencies.length"
        class="tac notice"
      >
        Вы еще не создали ни одной валюты
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { CURRENCY_MODULE } from '@/store/modules/TYPES'
import CurrencyPopup from '@/components/popups/CurrencyPopup'
const { GET_CURRENCIES, CURRENCIES, REMOVE_CURRENCY } = CURRENCY_MODULE

export default {
  components: { CurrencyPopup },
  data () {
    return {
      isPopupActive: false,
      isLoading: false,
      editableCurrency: null
    }
  },
  computed: {
    ...mapState({
      currencies: store => store.currencyModule[CURRENCIES]
    })
  },
  methods: {
    ...mapActions([GET_CURRENCIES, REMOVE_CURRENCY]),
    fetchData () {
      return this[GET_CURRENCIES]()
    },
    edit (currency) {
      this.editableCurrency = currency
    },
    close () {
      this.editableCurrency = null
      this.isPopupActive = false
    },
    confirmDelete (currency) {
      this.$buefy.dialog.confirm({
        title: 'Удаление валюты',
        message: 'Вы действительно хотите удалить валюту? Это действие нельзя отменить.',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.delete(currency)
      })
    },
    delete (currency) {
      this.isLoading = true
      this[REMOVE_CURRENCY](currency.id).then(() => {
        this.$buefy.toast.open('Валюта удалена')
      }).catch(e => {
        this.$buefy.toast.open('Валюта не удалена')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .currencies-list {
    margin-top: 10px;
  }
</style>
