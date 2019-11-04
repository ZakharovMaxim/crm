const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false }

export function extendFormat (date) {
  const today = new Date()
  const yesterday = new Date()
  yesterday.setDate(yesterday.getDate() - 1)
  let d = new Date(date)
  const timeoptions = { hour: '2-digit', minute: '2-digit', hour12: false }
  const time = d.toLocaleDateString('ru-UA', timeoptions).split(', ')[1]
  if (d.getDate() === today.getDate() && d.getMonth() === today.getMonth() && d.getFullYear() === today.getFullYear()) {
    const m = (today - d) / 1000 / 60
    console.log(m)
    if (m < 60) {
      return `${Math.ceil(m)} минут назад`
    } else {
      return `Сегодня в ${time}`
    }
  } else if (d.getDate() === yesterday.getDate() && d.getMonth() === yesterday.getMonth() && d.getFullYear() === yesterday.getFullYear()) {
    return `Вчера в ${time}`
  } else return simpleFormat(date, true)
}

export function dbFormat (date, noTime) {
  let d = new Date(date)
  let result = d.toLocaleDateString('ru-UA', options).replace(/\./ig, '-').replace(',', '')
  const dateSplit = result.split(' ')[0]
  const timeSplit = result.split(' ')[1]
  return dateSplit.split('-').reverse().join('-') + (noTime ? '' : ' ' + timeSplit)
}
export function simpleFormat (date, withTime) {
  let d = new Date(date)
  let result = d.toLocaleDateString('ru-UA', options).replace(/\./ig, '/').replace(',', '')
  const dateSplit = result.split(' ')[0]
  return dateSplit + (withTime ? ' ' + result.split(' ')[1] : '')
}
export function setMonth () {
  const firstDate = new Date()
  firstDate.setDate(1)
  const secondDate = new Date()
  secondDate.setMonth(secondDate.getMonth() + 1)
  secondDate.setDate(0)
  return [firstDate, secondDate]
}

export const monthes = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
export const days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']

export function monthName (shift = 0) {
  const date = new Date()
  date.setMonth(date.getMonth() - shift)
  return `${monthes[date.getMonth()]} ${date.getFullYear()}`
}

export function isToday (date) {
  const today = new Date()
  return today.getDate() === date.getDate() && today.getMonth() === date.getMonth() && today.getFullYear() === date.getFullYear()
}

export function isYesterday (date) {
  const today = new Date()
  today.setDate(today.getDate() - 1)
  return today.getDate() === date.getDate() && today.getMonth() === date.getMonth() && today.getFullYear() === date.getFullYear()
}

export function isMonthPeriod (date1, date2) {
  const lastDate = new Date(date1)
  lastDate.setMonth(lastDate.getMonth() + 1)
  lastDate.setDate(0)
  console.log(lastDate)
  return (date1.getMonth() === date2.getMonth()) && (date1.getFullYear() === date2.getFullYear()) && date1.getDate() === 1 && date2.getDate() === lastDate.getDate()
}

export function npFormat (date) {
  const result = simpleFormat(date).replace(/\//ig, '.')
  return result
}
