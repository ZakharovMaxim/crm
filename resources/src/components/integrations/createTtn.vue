<template>
  <div>
    Создать ттн
    <div v-if="isLoading">
      Подготавливаем данные для создания накладной
    </div>
    <div v-else>
      <b-loading
        :active="isSaving"
      />
      <b-field
        :type="form.getBuefyType('date')"
        :message="form.errors.get('date')"
        label="Дата отправки"
      >
        <b-datepicker
          v-model="form.date"
        />
      </b-field>
      <b-field
        :type="form.getBuefyType('delivery_type')"
        :message="form.errors.get('delivery_type')"
        label="Тип доставки"
      >
        <b-select
          v-model="form.delivery_type"
          icon="package"
        >
          <option value="1">
            Отделение > Отделение
          </option>
          <option value="2">
            Отделение > Адрес
          </option>
        </b-select>
      </b-field>
      <b-field
        :type="form.getBuefyType('payer')"
        :message="form.errors.get('payer')"
        label="Плательщик"
      >
        <b-select
          v-model="form.payer"
          icon="account"
        >
          <option value="Sender">
            Отправитель
          </option>
          <option value="Recipient">
            Получатель
          </option>
        </b-select>
      </b-field>
      <b-field
        :type="form.getBuefyType('description')"
        :message="form.errors.get('description')"
        label="Описание"
      >
        <b-input
          v-model="form.description"
          type="textarea"
        />
      </b-field>
      <div class="is-flex">
        <b-field
          :type="form.getBuefyType('size_x')"
          :message="form.errors.get('size_x')"
          label="X, мм"
        >
          <b-input
            v-model="form.size_x"
            icon="move-resize"
          />
        </b-field>
        <b-field
          :type="form.getBuefyType('size_y')"
          :message="form.errors.get('size_y')"
          label="Y, мм"
        >
          <b-input v-model="form.size_y" />
        </b-field>
        <b-field
          :type="form.getBuefyType('size_z')"
          :message="form.errors.get('size_z')"
          label="Z, мм"
        >
          <b-input v-model="form.size_z" />
        </b-field>
      </div>
      <div class="is-flex">
        <b-field
          :type="form.getBuefyType('weight')"
          :message="form.errors.get('weight')"
          label="Факт. вес, кг:"
        >
          <b-input
            v-model="form.weight"
            icon="weight"
          />
        </b-field>
        <b-field
          :type="form.getBuefyType('estimated_price')"
          :message="form.errors.get('estimated_price')"
          label="Оцен стоимость:"
        >
          <b-input
            v-model="form.estimated_price"
            icon="coins"
          />
        </b-field>
      </div>
      <div>
        <div class="form-subtitle">
          Данные <span>отправителя</span>
        </div>
        <b-field
          :type="form.getBuefyType('sender')"
          :message="form.errors.get('sender')"
          label="Отправитель"
        >
          <b-select
            v-model="form.sender"
            icon="account"
            @input="setSender"
          >
            <option
              v-for="sender in senders"
              :key="sender.ref"
              :value="sender.ref"
            >
              {{ sender.name }}
            </option>
          </b-select>
        </b-field>
        <np-warehouse-select
          :cities="cities"
          :types="[form.getBuefyType('sender_city'), form.getBuefyType('sender_warehouse')]"
          :errors="[form.errors.get('sender_city'), form.errors.get('sender_warehouse')]"
          :warehouse="form.sender_warehouse"
          :city="form.sender_city"
          :api-key="apiKey"
          @select-city="opt => form.sender_city = opt"
          @select-warehouse="opt => form.sender_warehouse = opt"
        />
      </div>
      <div>
        <div class="form-subtitle">
          Данные <span>получателя</span>
        </div>
        <div class="is-flex gapless is-multiline">
          <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
            <b-field
              :type="form.getBuefyType('recipient_name')"
              :message="form.errors.get('recipient_name')"
              label="Имя"
            >
              <b-input
                v-model="form.recipient_name"
                placeholder="Имя"
                icon="account"
              />
            </b-field>
          </div>
          <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
            <b-field
              :type="form.getBuefyType('recipient_surname')"
              :message="form.errors.get('recipient_surname')"
              label="Фамилия"
            >
              <b-input
                v-model="form.recipient_surname"
                placeholder="Фамилия"
                icon="account"
              />
            </b-field>
          </div>
          <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
            <b-field
              :type="form.getBuefyType('recipient_phone')"
              :message="form.errors.get('recipient_phone')"
              label="Номер телефона"
            >
              <b-input
                v-model="form.recipient_phone"
                placeholder="Номер телефона"
                icon="phone"
              />
            </b-field>
          </div>
          <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
            <b-field
              :type="form.getBuefyType('recipient_fathername')"
              :message="form.errors.get('recipient_fathername')"
              label="Отчество"
            >
              <b-input
                v-model="form.recipient_fathername"
                placeholder="Отчество"
                icon="account"
              />
            </b-field>
          </div>
        </div>
        <np-warehouse-select
          :cities="cities"
          :types="[form.getBuefyType('recipient_city'), form.getBuefyType('recipient_warehouse')]"
          :errors="[form.errors.get('recipient_city'), form.errors.get('recipient_warehouse')]"
          :warehouse="form.recipient_warehouse"
          :city="form.recipient_city"
          :api-key="apiKey"
          @select-city="opt => form.recipient_city = opt"
          @select-warehouse="opt => form.recipient_warehouse = opt"
        />
        <div v-if="form.delivery_type === '2'">
          <b-field
            :type="form.getBuefyType('recipient_address')"
            :message="form.errors.get('recipient_address')"
            label="Адрес"
          >
            <b-input
              v-model="form.recipient_address"
            />
          </b-field>
          <div class="is-flex">
            <b-field
              :type="form.getBuefyType('recipient_house')"
              :message="form.errors.get('recipient_house')"
              label="Дом"
            >
              <b-input
                v-model="form.recipient_house"
              />
            </b-field>
            <b-field
              :type="form.getBuefyType('recipient_flat')"
              :message="form.errors.get('recipient_flat')"
              label="Квартира"
            >
              <b-input
                v-model="form.recipient_flat"
              />
            </b-field>
          </div>
        </div>
      </div>
      <div>
        <div class="form-subtitle">
          <span>Обратная</span> доставка
        </div>
        <div class="is-flex is-multiline">
          <div class="column is-6">
            <b-field
              :type="form.getBuefyType('backward_payer')"
              :message="form.errors.get('backward_payer')"
              label="Плательщик"
            >
              <b-select
                v-model="form.backward_payer"
                icon="account"
              >
                <option value="Sender">
                  Отправитель
                </option>
                <option value="Recipient">
                  Получатель
                </option>
              </b-select>
            </b-field>
          </div>
          <div class="column is-6">
            <b-field
              :type="form.getBuefyType('backward_price')"
              :message="form.errors.get('backward_price')"
              label="Сумма"
            >
              <b-input
                v-model="form.backward_price"
                icon="coins"
              />
            </b-field>
          </div>
        </div>
      </div>
      <b-button @click="submit">
        Создать
      </b-button>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { NP_MODULE } from '@/store/modules/TYPES'
import validation from '@/validations/ttn'
import npWarehouseSelect from './npWarehouseSelect'
import { npFormat } from '@/helpers/DateFormat'
const { CREATE_TNN_FORM, CITIES, SENDERS, CREATE_TTN } = NP_MODULE

export default {
  components: {
    npWarehouseSelect
  },
  props: {
    apiKey: {
      type: String,
      default: ''
    },
    order: {
      type: Object,
      required: true
    },
    orderSum: {
      type: Number,
      required: true
    }
  },
  data () {
    return {
      isLoading: false,
      isSaving: false,
      city: '',
      form: validation()
    }
  },
  computed: {
    ...mapState({
      cities: store => store.orderModule[CITIES],
      senders: store => store.orderModule[SENDERS]
    })
  },
  watch: {
    'form.delivery_type': function (v) {
      const required = v === '2'
      this.form.changeValidateRule('recipient_address', 'required', required)
      this.form.changeValidateRule('recipient_house', 'required', required)
      this.form.changeValidateRule('recipient_flat', 'required', required)
    }
  },
  created () {
    this.form.recipient_name = this.order.customer_name
    this.form.recipient_surname = this.order.customer_surname
    this.form.recipient_fathername = this.order.customer_fathername
    this.form.recipient_phone = this.order.customer_phone
    this.form.estimated_price = this.orderSum
    if (this.order.delivery_id === 2) {
      this.form.delivery_type = '2'
    }
    if (this.order.delivery_payer) {
      const payer = +this.order.delivery_payer === 1 ? 'Sender' : 'Recipient'
      this.form.payer = payer
      this.form.backward_payer = payer
    }
    this.isLoading = true
    this[CREATE_TNN_FORM](this.order.id).finally(() => {
      this.isLoading = false
    })
  },
  methods: {
    ...mapActions([CREATE_TNN_FORM, CREATE_TTN]),
    setSender (senderRef) {
      if (!senderRef) return

      const sender = this.senders.find(send => send.ref === senderRef)
      this.form.sender_phone = sender.phone
      this.form.sender_contact_ref = sender.contact_ref
    },
    submit () {
      if (!this.form.validate()) return
      this.isSaving = true
      const data = this.form.data()
      this[CREATE_TTN]({
        orderId: this.order.id,
        data: {
          ...data,
          date: npFormat(data.date),
          key: this.apiKey,
          sender_warehouse_ref: data['sender_warehouse']['code'],
          sender_city_ref: data['sender_city']['code'],
          recipient_warehouse_ref: data['recipient_warehouse']['code'],
          recipient_city_ref: data['recipient_city']['code']
        }
      }).then(e => {
        this.$buefy.toast.open('Накладная успешно создана')
      }).catch(e => {
        console.log(e)
        this.$buefy.toast.open('Что-то пошло не так, проверьте правильность заполнения формы')
        if (e.response && e.response.data) this.form.setErrors(e.response.data.errors || {})
      }).finally(() => {
        this.isSaving = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.form-subtitle {
  text-align: center;
  margin-top: 10px;
  span {
    color: #008fe2;
    font-weight: bold;
  }
}
</style>
