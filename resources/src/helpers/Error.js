export default class Error {
  constructor (obj = {}) {
    this.errors = obj
  }
  /**
   * clear all errors
   */
  reset () {
    for (let key in this.errors) this.errors[key] = ''
  }
  /**
   * check error by field
   * @returns {Boolean}
   */
  has (key) {
    if (this.errors[key]) return !!this.errors[key]
  }
  /**
   * check error by field
   * @returns {String}
   */
  get (key) {
    if (this.errors[key]) return this.errors[key]
  }
  /**
   * add new error object (if u use server validation whick return errors obj it can be useful)
   */
  add (errors) {
    this.errors = errors
  }
  /**
   * set error field
   * @param {String} key
   * @param {String} value
   */
  setOne (key, value) {
    this.errors[key] = value
  }
  /**
   * clear error field and clear 'global' form error
   * @param {String} key
   */
  remove (key) {
    if (this.errors[key]) this.errors[key] = ''
    this.errors['form'] = ''
  }
  /**
   * check if it has any errors
   * @returns {Boolean}
   */
  any () {
    let hasError = false
    for (let key in this.errors) {
      if (this.errors[key]) hasError = true
    }
    return hasError
  }
}
