function queryBuilder (obj) {
  if (!obj) return ''
  const keys = Object.keys(obj)
  if (!keys.length) return ''

  let result = ''
  for (let i = 0; i < keys.length; i++) {
    console.log(obj[keys[i]], keys[i])
    if (obj[keys[i]] === undefined || obj[keys[i]] === null || obj[keys[i]] === '') continue
    if (i !== 0) result += '&'
    result += keys[i] + '=' + obj[keys[i]]
  }
  return result ? `?${result}` : ''
}
export default queryBuilder
