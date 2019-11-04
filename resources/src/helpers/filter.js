import { dbFormat } from '@/helpers/DateFormat'

export default function filter (context, fields) {
  this.rows = {}
  this.query = context.$route.query

  fields.forEach(field => {
    field.type = field.type || 'list'
    let data = null
    if (this.query[field.items]) {
      if (field.type === 'list') {
        data = this.query[field.items].split(',')
      } else {
        data = this.query[field.items]
      }
    } else {
      if (field.type === 'date') {
        data = {
          from: this.query[`${field.items}_from`] ? new Date(this.query[`${field.items}_from`]) : null,
          to: this.query[`${field.items}_to`] ? new Date(this.query[`${field.items}_to`]) : null
        }
      }
    }
    this.rows[field.items] = {
      label: field.label,
      displayName: field.displayName || 'name',
      items: [],
      data,
      type: field.type,
      fetch: field.fetch,
      addNullOption: !!field.addNullOption
    }
  })

  this.update = () => {
    this.info = null
    for (let key in this.rows) {
      const item = this.rows[key]
      const { type, addNullOption, displayName } = item
      let data = null
      if (item.data && type !== 'removable') {
        data = item.data
      } else if (type === 'list') {
        data = (context[key] && context[key].map) ? context[key].map(item => item.id) : []
      } else if (type === 'removable') {
        if (item.data) {
          data = item.data
          const parts = item.fetch.split('.')
          let acc = context[parts[0]]
          parts.forEach(part => {
            if (acc && acc[part]) {
              acc = acc[part]
            }
          })
          if (acc) {
            item.items = acc
          }
        }
      }
      const items = type === 'removable' ? item.items : addNullOption ? [{ id: null, [displayName]: 'Не выбрано' }, ...context[key]] : context[key]
      this.rows[key] = {
        ...item,
        items,
        data
      }
    }
  }
  function getIds (arr) {
    if (!arr || !arr.length) return ''
    return arr.slice().sort((a, b) => a - b).map(id => id || 0).join(',')
  }
  function parse (rows) {
    const result = {}
    for (let key in rows) {
      const { data, type, items } = rows[key]
      if (type === 'list') {
        const list = getIds(data)
        const fullList = getIds(items.map(item => item.id))
        if (list !== fullList) {
          result[key] = list
        }
      } else if (type === 'date') {
        if (data.from) result[`${key}_from`] = dbFormat(data.from, true)
        if (data.to) result[`${key}_to`] = dbFormat(data.to, true)
      } else {
        result[key] = data
      }
    }
    return result
  }
  this.get = () => {
    return parse(this.rows)
  }

  this.apply = () => {
    console.log(this.rows)
    const newQuery = parse(this.rows)
    const query = {
      ...context.$route.query,
      ...newQuery
    }
    for (let key in this.rows) {
      if (!newQuery[key]) delete query[key]
    }
    context.$router.push({
      query
    })
  }

  this.getParams = () => {
    const result = []
    for (let key in this.rows) {
      const { data, type, items, label, displayName } = this.rows[key]
      if (type === 'list') {
        const list = getIds(data)
        const fullList = getIds(items.map(item => item.id))
        if ((list !== fullList) && list) {
          result.push({
            label,
            values: items.filter(item => data.includes('' + item.id)).map(item => item[displayName]).join(', ')
          })
        }
      } else if (type === 'date') {
        if (data.from && data.to) {
          result.push({
            label,
            values: `${dbFormat(data.from, true)}/${dbFormat(data.to, true)}`
          })
        }
      } else if (type === 'single' && data) {
        const item = items.find(item => +item.id === +data)
        if (item) {
          result.push({
            label,
            values: item ? item[displayName] : ''
          })
        }
      } else if (type === 'removable') {
        if (items && items.name) {
          const values = `${items.name}${items.additional_info ? ('(' + items.additional_info + ')') : ''}`
          result.push({
            label,
            values
          })
        }
      }
    }
    if (!this.info) {
      this.info = result
    } else {
      return this.info
    }
    return result
  }
}
