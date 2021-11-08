import api from './api'

export default {
  updateSettings (data) {
    return api.patch('/api/auth/settings', data)
  }
}
