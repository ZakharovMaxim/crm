import Form from '@/helpers/Form'

export default function (fields) {
  const data = {}
  fields.forEach(field => {
    data[field.key] = {
      required: true
    }
  })
  return new Form(data)
}
