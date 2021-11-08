import api from './api'

export default {
  getNotes (params) {
    return api.get('/api/notes', params)
  },

  saveNote (data) {
    return api.post('/api/notes', data)
  },

  updateNote (data) {
    return api.patch(`/api/notes/${data.id}`, data.data)
  },

  deleteNote (data) {
    return api.delete(`/api/notes/${data.id}`, {})
  },

  forceDeleteNote (data) {
    return api.delete(`/api/notes/${data.id}/force`, {})
  },

  restoreNote (data) {
    return api.patch(`/api/notes/${data.id}/restore`, {})
  },

  emptyTrash (data) {
    return api.delete('/api/notes', {})
  }
}
