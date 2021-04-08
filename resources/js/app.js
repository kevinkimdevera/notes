require('./bootstrap');

import Vue from 'vue';

// =============================================================================
// Vuetify
// =============================================================================
import vuetify from './plugins/vuetify';

// =============================================================================
// VUEX Store
// =============================================================================
import store from './store';

// =============================================================================
// Router
// =============================================================================
import router from './router';

Vue.component('app', require('./layouts/App.vue').default);

Vue.config.productionTip = false

const app = new Vue({
  el: '#app',
  vuetify,
  store,
  router
})


