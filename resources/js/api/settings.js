import api from './api'

export default {
  updateSettings(data) {
    return api.patch('settings', data)
  },
}
