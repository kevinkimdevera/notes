import notesApi from '../../api/notes'

const state = {
  notes: null,
  loading: false,
  saving: false
}

const getters = {
  NOTES: state => state.notes,
  LOADING: state => state.loading,
  SAVING: state => state.saving
}

const mutations = {
  SET_NOTES: (state, payload) => { state.notes = payload },
  SET_LOADING: (state, payload) => { state.loading = payload },
  SET_SAVING: (state, payload) => { state.saving = payload }
}

const actions = {
  CHANGE_LOADING_STATUS: ({ commit }, data) => {
    commit('SET_LOADING', data)
  },
  CHANGE_SAVING_STATUS: ({ commit }, data) => {
    commit('SET_SAVING', data)
  },
  CLEAR_NOTES: ({ commit }) => {
    commit('SET_NOTES', null)
  },
  GET: async ({ commit }, params) => {
    commit('SET_LOADING', true)
    const response = await notesApi.getNotes(params)
    commit('SET_LOADING', false)
    commit('SET_NOTES', response.data?.notes)
  },
  SAVE: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.saveNote(data)
    commit('SET_SAVING', false)
    return response.data?.saved
  },
  UPDATE: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.updateNote(data)
    commit('SET_SAVING', false)
    return response.data?.updated
  },
  DELETE: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.deleteNote(data)
    commit('SET_SAVING', false)
    return response.data?.deleted
  },
  DELETE_FOREVER: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.forceDeleteNote(data)
    commit('SET_SAVING', false)
    return response.data?.deleted
  },
  RESTORE: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.restoreNote(data)
    commit('SET_SAVING', false)
    return response.data?.restored
  },
  EMPTY_TRASH: async ({ commit }, data) => {
    commit('SET_SAVING', true)
    const response = await notesApi.emptyTrash()
    commit('SET_SAVING', false)
    return response.data?.deleted
  }
}

export default ({
  namespaced: true,
  state,
  getters,
  actions,
  mutations
})
