import Form from '@/helpers/Form'

export default function () {
  return new Form({
    shop_id: {
      required: true
    },
    bill_id: {
      required: true
    },
    payment_category_id: {
      required: true
    },
    sum: {
      required: true
    },
    date: {
      value: new Date()
    },
    time: {
      value: new Date()
    }
  })
}
