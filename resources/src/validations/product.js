import Form from '@/helpers/Form'

export default function (isVariation) {
  const settings = {
    name: {
      required: true
    },
    additional_info: {
      value: ''
    },
    purchase_price: {
      type: 'number'
    },
    selling_price: {
      type: 'number'
    },
    min_count: {
      type: 'number'
    }
  }
  if (isVariation) {
    settings.sku = ''
  } else {
    settings.sku = {
      required: true
    }
  }
  return new Form(settings)
}
