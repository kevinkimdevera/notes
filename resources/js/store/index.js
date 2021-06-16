import Vue from 'vue'
import Vuex from 'vuex'

import notes from './modules/notes'
import settings from './modules/settings'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    notes,
    settings
  }
})

export default store
