import settingsApi from '../../api/settings'

const state = {
  loading: false
}

const getters = {
  LOADING: state => state.loading
}

const mutations = {
  SET_LOADING: (state, payload) => { state.loading = payload }
}

const actions = {
  UPDATE: async ({ commit }, data) => {
    commit('SET_LOADING', true)
    return await new Promise((resolve, reject) => {
      settingsApi.updateSettings(data)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
        .finally(() => {
          commit('SET_LOADING', false)
        })
    })
  }
}

export default ({
  namespaced: true,
  state,
  getters,
  actions,
  mutations
})
