import api from './api'

export default {
  getSettings (data) {
    return api.get('settings')
  },

  updateSettings (data) {
    return api.patch('settings', data)
  }
}
