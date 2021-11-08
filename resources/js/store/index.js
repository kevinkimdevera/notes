import Vue from 'vue'
import Vuex from 'vuex'

const moduleFiles = require.context('./modules', true, /\.js$/)

const modules = moduleFiles.keys().reduce((modules, modulePath) => {
  const moduleName = modulePath.replace(/^\.\/(.*)\.\w+$/, '$1')
  const value = moduleFiles(modulePath)
  modules[moduleName] = value.default

  return modules
}, {})

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: modules
})

export default store
