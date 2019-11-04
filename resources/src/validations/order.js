import Form from '@/helpers/Form'

export default function () {
  return new Form({
    shop_id: {
      required: true
    },
    customer_name: {
      value: ''
    },
    customer_surname: {
      value: ''
    },
    customer_fathername: {
      value: ''
    },
    customer_phone: {
      value: ''
    },
    customer_email: {
      value: ''
    },
    payment_type_id: {
      value: ''
    },
    bill_id: {
      value: ''
    },
    delivery_id: {
      value: ''
    },
    delivery_payer: {
      value: ''
    },
    delivery_city: {
      value: ''
    },
    user_comment: {
      value: ''
    },
    order_comment: {
      value: ''
    },
    payment_source_link: {
      value: ''
    },
    payment_source_id: {
      value: ''
    },
    roistat_visit_id: {
      value: ''
    },
    check_comment: {
      value: ''
    },
    delivery_address: {
      value: ''
    }
  })
}
