import Form from '@/helpers/Form'

export default function () {
  return new Form({
    name: {
      required: true,
      value: ''
    },
    catalog_id: {
      required: true,
      value: ''
    }
  })
}
