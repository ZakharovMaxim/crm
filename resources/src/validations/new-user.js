import Form from '@/helpers/Form'

export default function (type) {
  const fields = {
    login: {
      required: true,
      min: 6
    },
    name: {
      required: true
    },
    surname: {
      required: true
    },
    password: {
      required: true,
      min: 6
    },
    role: {
      required: true
    },
    password_confirmation: {
      confirm: {
        field: 'password'
      }
    }
  }
  if (type === 'password') {
    delete fields['login']
    delete fields['name']
    delete fields['surname']
    delete fields['role']
  } else if (type === 'info') {
    delete fields['password']
    delete fields['password_confirmation']
    delete fields['role']
  }
  return new Form(fields)
}
