const plugins = {
  'np': {
    label: 'Новая почта',
    fields: [
      {
        key: 'api_key',
        label: 'Ключ api'
      }
    ],
    icon: '/np.svg',
    description: 'Это транспортно-логистические услуги по всей Украине, быстрая и надежная доставка документов, корреспонденции и писем по Украине.'
  },
  'turbo': {
    label: 'TurboSMS',
    fields: [
      {
        key: 'sign',
        label: 'Имя отправителя (альфа-имя):'
      },
      {
        key: 'login',
        label: 'Логин от SMS шлюза:'
      },
      {
        key: 'password',
        label: 'Пароль от SMS шлюза:'
      }
    ],
    icon: '/turbo.jpg',
    description: 'Это сервис массовых рассылок СМС-сообщений вашим клиентам, заставьте свой бизнес работать!'
  },
  'roi': {
    label: 'Roistat',
    fields: [
      {
        key: 'login',
        label: 'Имя пользователя:'
      },
      {
        key: 'password',
        label: 'Пароль пользователя:'
      }
    ],
    icon: '/roi.png',
    description: 'Roistat – это сервис сквозной бизнес-аналитики. Данный плагин позволяет передавать данные из exoCRM в Roistat для аналитики.'
  }
}

export function getPlugin (type) {
  return plugins[type]
}
export default plugins
