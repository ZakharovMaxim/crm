import Form from '@/helpers/Form'

export default function () {
  return new Form({
    login: {
      required: true,
      min: 6
    },
    password: {
      required: true,
      min: 6
    }
  })
}
