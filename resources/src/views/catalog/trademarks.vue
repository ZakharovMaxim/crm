<template>
  <GetData :callback="fetchData">
    <div v-title="'Торговые марки'">
      <button
        slot="trigger"
        class="button is-primary"
        @click="isPopupActive = true"
      >
        <b-icon
          icon="plus"
          size="is-large"
          title="Создать торговую марку"
          class="is-action"
        />
      </button>
      <trademark-popup
        v-if="isPopupActive || !!editableTrademark"
        :is-active="isPopupActive || !!editableTrademark"
        :trademark="editableTrademark"
        @close="close"
      />
      <div
        v-if="trademarks.length"
        class="trademarks-list"
      >
        <div
          v-for="trademark in trademarks"
          :key="'trademark_' + trademark.id"
          class="card editable-card"
        >
          <div
            class="card-content"
            @click="edit(trademark)"
          >
            {{ trademark.name }}
            <div class="card-tools">
              <b-tooltip
                label="Изменить торговую марку"
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
                label="Удалить торговую марку"
                position="is-top"
              >
                <div
                  class="card-tool card-tool--danger"
                  @click.stop="confirmDelete(trademark)"
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
        v-if="!trademarks.length"
        class="tac notice"
      >
        Вы еще не создали ни одной торговой марки
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { TRADEMARK_MODULE } from '@/store/modules/TYPES'
import TrademarkPopup from '@/components/popups/TrademarkPopup'
const { GET_TRADEMARKS, TRADEMARKS, REMOVE_TRADEMARK } = TRADEMARK_MODULE

export default {
  components: { TrademarkPopup },
  data () {
    return {
      isPopupActive: false,
      isLoading: false,
      editableTrademark: null
    }
  },
  computed: {
    ...mapState({
      trademarks: store => store.trademarkModule[TRADEMARKS]
    })
  },
  methods: {
    ...mapActions([GET_TRADEMARKS, REMOVE_TRADEMARK]),
    fetchData () {
      return this[GET_TRADEMARKS]()
    },
    edit (trademark) {
      this.editableTrademark = trademark
    },
    close () {
      this.editableTrademark = null
      this.isPopupActive = false
    },
    confirmDelete (trademark) {
      this.$buefy.dialog.confirm({
        title: 'Удаление торговой марки',
        message: 'Вы действительно хотите удалить торговую марку? Это действие нельзя отменить.',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.delete(trademark)
      })
    },
    delete (trademark) {
      this.isLoading = true
      this[REMOVE_TRADEMARK](trademark.id).then(() => {
        this.$buefy.toast.open('Торговая марка удалена')
      }).catch(e => {
        this.$buefy.toast.open('Торговая марка не удалена')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .trademarks-list {
    margin-top: 10px;
  }
</style>
