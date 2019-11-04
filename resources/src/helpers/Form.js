import Errors from './Error.js'

export default class Form {
  static isNumber (n) {
    return !isNaN(n) && isFinite(n)
  }
  constructor (data) {
    this.originalData = data
    this.validations = []
    const errors = {}
    for (let field in data) {
      if (typeof data[field] === 'object') {
        this.validations[field] = data[field]
        if (this.validations[field].value !== undefined && this.validations[field].value !== null) {
          this[field] = this.validations[field].value
        } else {
          this[field] = ''
        }
      } else {
        this[field] = ''
      }
      errors[field] = ''
    }
    errors['form'] = ''
    this.errors = new Errors(errors)
  }
  /**
   * reset all form fields
   */
  reset () {
    for (let field in this.originalData) {
      this[field] = ''
    }
    this.errors.reset()
  }
  submit () {
    if (this.validate()) {
      console.log(this.validate())
      console.log(this.data())
      return this.data()
    }
  }
  /**
   * return only data what should be sended on server
   */
  data () {
    let r = Object.assign({}, this)
    delete r.originalData
    delete r.errors
    delete r.validations
    return r
  }
  dataFilled () {
    const data = this.data()
    for (let field in data) {
      if (!data[field]) delete data[field]
    }
    return data
  }
  setData (newData) {
    for (let field in newData) {
      if (newData[field] !== undefined) this[field] = newData[field]
    }
  }
  formDataFilled () {
    const data = this.data()
    const formData = new FormData()
    Object.keys(data).forEach(key => {
      if (data[key]) formData.append(key, data[key])
    })
    return formData
  }
  formData () {
    const data = this.data()
    const formData = new FormData()
    Object.keys(data).forEach(key => {
      formData.append(key, data[key])
    })
    return formData
  }
  /**
   * change validation rule
   * @param {String} fieldName
   * @param {String} ruleName
   * @param {any} value
   */
  changeValidateRule (fieldName, ruleName, value) {
    this.validations[fieldName][ruleName] = value
  }
  /**
   * iterate all validations rules and add error if some fields have errors
   * @param {Object} data data to check
   */
  validate (data = this.data()) {
    this.errors.reset()
    const rules = this.validations
    console.log(this.validations)
    for (let field in rules) {
      const rule = rules[field]
      if (rule.ignore) continue
      if (rule.max && data[field].length > rule.max) this.errors.setOne(field, 'Максимальная длина ' + rule.max + ' символов')
      if (rule.min && data[field].length < rule.min) this.errors.setOne(field, 'Минимальная длина ' + rule.min + ' символов')
      if (!Form.isEmpty(data[field]) && rule.greaterThan !== undefined && data[field] <= rule.greaterThan) {
        this.errors.setOne(field, 'Значение должно быть больше ' + rule.greaterThan)
      }
      if (rule.required && Form.isEmpty(data[field])) {
        this.errors.setOne(field, 'Поле обязательно для заполнения')
      }
      if (rule.type === 'number' && data[field] && !Form.isNumber(data[field])) {
        this.errors.setOne(field, 'Введите число')
      }
      if (rule.confirm && data[rule.confirm.field] !== data[field]) this.errors.setOne(field, rule.confirm.errorMsg || 'Поля не совпадают')
    }
    return !this.errors.any()
  }
  setErrors (e) {
    if (e && e.response && e.response.data && e.response.data.errors) {
      const { errors } = e.response.data
      const data = this.data()
      for (let field in errors) {
        if (data[field] !== undefined) this.errors.setOne(field, errors[field][0] || '')
      }
    } else {
      this.errors.setOne('form', e.message || '')
    }
  }
  setError (field, error) {
    this.errors.setOne(field, error)
  }
  getBuefyType (name) {
    if (this.errors.has(name)) {
      return 'is-danger'
    } else return ''
  }
}

Form.isEmpty = function (v) {
  const type = typeof v
  return (v === undefined || v === null || v === '') || (type === 'object' && !Object.keys(v).length && !(v instanceof Date))
}
