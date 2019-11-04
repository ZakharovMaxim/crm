<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать атрибут
    </template>
    <template slot="content">
      <b-field
        label="Название атрибута"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название атрибута"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
      <div>
        <div
          v-for="(value, index) in values"
          :key="'value_' + index"
          class="attribute"
        >
          <div class="attribute--val">
            {{ value }}
          </div>
          <div class="attribute--tools">
            <b-tooltip
              label="Удалить"
              position="is-top"
            >
              <div
                class="is-action"
                @click="removeAttributeValue(index)"
              >
                <b-icon
                  icon="delete"
                />
              </div>
            </b-tooltip>
          </div>
        </div>
        <div class="attribute-create">
          <b-input
            v-model="attributeValue"
            type="text"
            @keydown.native.enter="addAttributeValue"
          />
          <button
            class="button is-primary"
            @click="addAttributeValue"
          >
            <b-icon
              icon="plus"
            />
          </button>
        </div>
      </div>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        Создать
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { ATTRIBUTE_MODULE } from '@/store/modules/TYPES'
const { CREATE_ATTRIBUTE } = ATTRIBUTE_MODULE

export default {
  props: {
    isActive: Boolean
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      values: [],
      attributeValue: ''
    }
  },
  methods: {
    ...mapActions([CREATE_ATTRIBUTE]),
    addAttributeValue () {
      if (!this.attributeValue) return
      this.values = [...this.values, this.attributeValue]
      this.attributeValue = ''
    },
    removeAttributeValue (index) {
      this.values = this.values.filter((value, i) => i !== index)
    },
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_ATTRIBUTE]({
        ...this.form.data(),
        values: this.values
      }).then(response => {
        this.form.name = ''
        this.values = []
        this.$buefy.toast.open('Атрибут создан')
        this.$emit('close')
      }).catch(e => {
        console.log(e)
        this.$buefy.toast.open('Атрибут не создан :(')
        this.form.setErrors(e.response)
      }).finally(() => {
        this.isLoading = false
      })
    },
    close () {
      this.$emit('close')
      this.values = []
      this.attributeValue = ''
      this.form.reset()
    }
  }
}
</script>

<style lang="scss" scoped>
  .attribute {
    display: flex;
    &-create {
      display: flex;
    }
  }
</style>
