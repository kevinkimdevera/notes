import settingsApi from '../../api/settings'

const state = {
  loading: false,
  settings: {
    view: 'grid',
    theme: 'light'
  }
}

const getters = {
  LOADING: state => state.loading,
  VIEW_MODE: state => state.settings.view,
  THEME: state => state.settings.theme
}

const mutations = {
  SET_LOADING: (state, payload) => { state.loading = payload },
  SET_SETTINGS: (state, payload) => { state.settings = payload },
  SET_VIEW: (state, payload) => { state.settings.view = payload },
  SET_THEME: (state, payload) => { state.settings.theme = payload }
}

const actions = {
  GET: async ({ commit }) => {
    const response = await settingsApi.getSettings()
    commit('SET_SETTINGS', response.data)
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

  CHANGE_THEME: async ({ commit }, data) => {
    commit('SET_THEME', data)

    commit('SET_LOADING', true)
    await settingsApi.updateSettings({
      key: 'theme',
      value: data
    })
    commit('SET_LOADING', false)
  }
}

export default ({
  namespaced: true,
  state,
  getters,
  actions,
  mutations
})
