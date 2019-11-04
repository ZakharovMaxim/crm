import validation from './validation'

export default function () {
  return validation([
    {
      name: {
        validation: {
          required: true
        },
        placeholder: 'Название магазина'
      },
      roistat_id: {
        placeholder: 'Интеграция с роистатом',
        type: 'select'
      },
      turbosms_id: {
        placeholder: 'Интеграция с турбосмс',
        type: 'select'
      },
      novaposhta_id: {
        placeholder: 'Интеграция с новой почтой',
        type: 'select'
      }
    }
  ])
}
