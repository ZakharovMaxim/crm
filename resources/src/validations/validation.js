import Form from '@/helpers/Form'

function reduceToForm (rows) {
  return rows.reduce((acc, row) => {
    for (let key in row) {
      acc[key] = (row[key] && row[key]['validation']) ? row[key]['validation'] : ''
    }
    return acc
  }, {})
}

export default function (rows) {
  return {
    rows,
    form: new Form(reduceToForm(rows))
  }
}
