import Form from '@/helpers/Form'

export default function () {
  return new Form({
    delivery_type: {
      required: true,
      value: '1'
    },
    payer: {
      required: true,
      value: '1'
    },
    package_type: {
      value: 'Cargo'
    },
    date: {
      required: true,
      value: new Date()
    },
    description: {
      value: 'Украшение'
    },
    size_x: {
      required: true,
      value: 100
    },
    size_y: {
      required: true,
      value: 10
    },
    size_z: {
      required: true,
      value: 100
    },
    weight: {
      required: true,
      value: 0
    },
    estimated_price: {
      required: true,
      greaterThan: 0
    },
    sender: {
      required: true
    },
    sender_city: {
      required: true,
      value: {
        name: 'Черновцы',
        code: 'e221d642-391c-11dd-90d9-001a92567626'
      }
    },
    sender_warehouse: {
      required: true,
      value: {
        name: 'Отделение №6 (до 30 кг): ул. Школьная, 1б',
        code: '1ec09d55-e1c2-11e3-8c4a-0050568002cf'
      }
    },
    recipient_name: {
      required: true
    },
    recipient_surname: {
      required: true
    },
    recipient_fathername: {
      required: true
    },
    recipient_phone: {
      required: true
    },
    recipient_city: {
      required: true,
      value: {}
    },
    recipient_warehouse: {
      required: true,
      value: {}
    },
    backward_payer: {
      value: '1'
    },
    backward_price: {},
    recipient_address: {},
    recipient_house: {},
    recipient_flat: {}
  })
}
