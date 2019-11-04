<template>
  <GetData :callback="fetchData">
    <div v-title="'Группы цен'">
      <button
        slot="trigger"
        class="button is-primary"
        @click="isPopupActive = true"
      >
        <b-icon
          icon="plus"
          size="is-large"
          title="Создать группу цен"
          class="is-action"
        />
      </button>
      <price-group-popup
        v-if="isPopupActive || !!editablePriceGroup"
        :is-active="isPopupActive || !!editablePriceGroup"
        :price-group="editablePriceGroup"
        @close="close"
      />
      <div
        v-if="priceGroups.length"
        class="priceGroups-list"
      >
        <div
          v-for="priceGroup in priceGroups"
          :key="'priceGroup_' + priceGroup.id"
          class="card editable-card"
        >
          <div
            class="card-content"
            @click="edit(priceGroup)"
          >
            {{ priceGroup.name }}
            <div class="card-tools">
              <b-tooltip
                label="Изменить группу"
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
                label="Удалить группу"
                position="is-top"
              >
                <div
                  class="card-tool card-tool--danger"
                  @click.stop="confirmDelete(priceGroup)"
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
        v-if="!priceGroups.length"
        class="tac notice"
      >
        Вы еще не создали ни одной группы
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PRICE_GROUP_MODULE } from '@/store/modules/TYPES'
import PriceGroupPopup from '@/components/popups/PriceGroupPopup'
const { GET_PRICE_GROUPS, PRICE_GROUPS, REMOVE_PRICE_GROUP } = PRICE_GROUP_MODULE

export default {
  components: { PriceGroupPopup },
  data () {
    return {
      isPopupActive: false,
      isLoading: false,
      editablePriceGroup: null
    }
  },
  computed: {
    ...mapState({
      priceGroups: store => store.priceGroupModule[PRICE_GROUPS]
    })
  },
  methods: {
    ...mapActions([GET_PRICE_GROUPS, REMOVE_PRICE_GROUP]),
    fetchData () {
      return this[GET_PRICE_GROUPS]()
    },
    edit (priceGroup) {
      console.log(priceGroup)
      this.editablePriceGroup = priceGroup
    },
    close () {
      this.editablePriceGroup = null
      this.isPopupActive = false
    },
    confirmDelete (priceGroup) {
      this.$buefy.dialog.confirm({
        title: 'Удаление группы',
        message: 'Вы действительно хотите удалить группу? Это действие нельзя отменить.',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.delete(priceGroup)
      })
    },
    delete (priceGroup) {
      this.isLoading = true
      this[REMOVE_PRICE_GROUP](priceGroup.id).then(() => {
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
  .priceGroups-list {
    margin-top: 10px;
  }
</style>
