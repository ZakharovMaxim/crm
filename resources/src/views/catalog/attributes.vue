<template>
  <GetData :callback="fetchData">
    <b-loading :active="isLoading" />
    <div v-title="'Атрибуты'">
      <button
        slot="trigger"
        class="button is-primary"
        @click="isAttributeCreateActive = true"
      >
        <b-icon
          icon="plus"
          size="is-large"
          title="Создать атрибут"
          class="is-action"
        />
      </button>
      <CreateAttributePopup
        :is-active="isAttributeCreateActive"
        @close="isAttributeCreateActive = false"
      />
      <UpdateAttributePopup
        v-if="!!editing"
        :is-active="!!editing"
        :attribute="editing"
        @close="editing = null"
      />
      <div
        v-if="attributes.length"
        class="attributes-list is-flex is-multiline"
      >
        <div
          v-for="attribute in attributes"
          :key="'attribute_' + attribute.id"
          class="column is-4-desktop is-4-tablet is-6-mobile"
          :title="attributeValues(attribute)"
          @click="edit(attribute)"
        >
          <Folder
            :id="attribute.id"
            :name="`${attribute.name} (${attribute.values.length})`"
            remove-title="Удаление характеристики"
            remove-message="Вы действительно хотите удалить характеристику?"
            label="характеристику"
            @update="edit(attribute)"
            @delete="onDelete(attribute)"
          />
        </div>
      </div>
      <div
        v-if="!attributes.length"
        class="tac notice"
      >
        Вы еще не создали ни одной характеристики
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { ATTRIBUTE_MODULE } from '@/store/modules/TYPES'
import CreateAttributePopup from '@/components/popups/CreateAttributePopup'
import UpdateAttributePopup from '@/components/popups/UpdateAttributePopup'
import Folder from '@/components/Cards/Folder'
const { GET_ATTRIBUTES, ATTRIBUTES, DESTROY_ATTRIBUTE } = ATTRIBUTE_MODULE

export default {
  components: { CreateAttributePopup, UpdateAttributePopup, Folder },
  data () {
    return {
      isAttributeCreateActive: false,
      editing: null,
      isLoading: false
    }
  },
  computed: {
    ...mapState({
      attributes: store => store.attributeModule[ATTRIBUTES]
    })
  },
  methods: {
    ...mapActions([GET_ATTRIBUTES, DESTROY_ATTRIBUTE]),
    fetchData () {
      return this[GET_ATTRIBUTES]()
    },
    edit (attribute) {
      this.editing = attribute
    },
    onDelete (attribute) {
      this.isLoading = true
      this[DESTROY_ATTRIBUTE](attribute.id).then(() => {
        this.$buefy.toast.open('Атрибут удален')
      }).catch(e => {
        this.$buefy.toast.open('Атрибут не удален')
      }).finally(() => {
        this.isLoading = false
      })
    },
    attributeValues (attribute) {
      return attribute.values.map(v => v.value).join(', ')
    }
  }
}
</script>

<style lang="scss" scoped>
  .attributes-list {
    margin-top: 10px;
  }
</style>
