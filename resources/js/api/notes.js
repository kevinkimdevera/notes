import api from './api'

export default {
  getNotes (params) {
    return api.get('notes', params)
  },

  saveNote (data) {
    return api.post('notes', data)
  },

  updateNote (data) {
    return api.patch(`notes/${data.id}`, data.data)
  },

  deleteNote (data) {
    return api.delete(`notes/${data.id}`, {})
  },

  forceDeleteNote (data) {
    return api.delete(`notes/${data.id}/force`, {})
  },

  restoreNote (data) {
    return api.patch(`notes/${data.id}/restore`, {})
  },

  emptyTrash (data) {
    return api.delete('notes', {})
  }
}
