import notesApi from '../../api/notes'
import settingsApi from '../../api/settings'

const state = {
  unpinned: null,
  pinned: null,
  archived: null,
  trashed: null,
  loading: false,
  saving: false,
  settings: {
    view: 'grid'
  },
}

const getters = {
  PINNED: state => state.pinned,
  UNPINNED: state => state.unpinned,
  ARCHIVED: state => state.archived,
  TRASHED: state => state.trashed,
  LOADING: state => state.loading,
  SAVING: state => state.saving,

  VIEW_MODE: state => state.settings.view,
}

const mutations = {
  SET_UNPINNED: (state, data) => ( state.unpinned = data ),
  SET_PINNED: (state, data) => ( state.pinned = data ),
  SET_ARCHIVED: (state, data) => (state.archived = data ),
  SET_TRASHED: (state, data) => ( state.trashed = data ),
  SET_LOADING: (state, data) => ( state.loading = data ),
  SET_SAVING: (state, data) => ( state.saving = data ),

  SET_SETTINGS: (state, data) => ( state.settings = data ),
  SET_VIEW: (state, data) => ( state.settings.view = data )
}

const actions = {
  CHANGE_LOADING_STATUS: ({ commit }, data) => {
    commit('SET_LOADING', data)
  },
  CHANGE_SAVING_STATUS: ({ commit }, data) => {
    commit('SET_SAVING', data)
  },
  CHANGE_VIEW_MODE: async ({ commit }, data) => {
    commit('SET_VIEW', data)

    commit('SET_LOADING', true)
    await settingsApi.updateSettings({
      key: 'view',
      value: data
    })
    commit('SET_LOADING', false)
  },
  GET: async ({ commit }) => {
    const response = await notesApi.getNotes()

    commit('SET_UNPINNED', response.data?.notes?.unpinned)
    commit('SET_PINNED', response.data?.notes?.pinned)
    commit('SET_ARCHIVED', response.data?.notes?.archived)
    commit('SET_TRASHED', response.data?.notes?.trashed)
    commit('SET_SETTINGS', response.data?.settings)
  },
  SAVE: async ({ commit }, data) => {
    const response = await notesApi.saveNote(data)
    return response.data?.saved
  },
  UPDATE: async ({ commit }, data) => {
    const response = await notesApi.updateNote(data)
    return response.data?.updated
  },
  DELETE: async({ commit }, data) => {
    const response = await notesApi.deleteNote(data)
    return response.data?.deleted
  },
  DELETE_FOREVER: async({ commit }, data) => {
    const response = await notesApi.forceDeleteNote(data)
    return response.data?.deleted
  },
  RESTORE: async({ commit }, data) => {
    const response = await notesApi.restoreNote(data)
    return response.data?.restored
  },
}

export default({
  namespaced: true,
  state,
  getters,
  actions,
  mutations
})
