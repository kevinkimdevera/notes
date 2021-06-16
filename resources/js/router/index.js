import Vue from 'vue'
import VueRouter from 'vue-router'

import Notes from '../pages/Notes.vue'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: '/',
  routes: [
    {
      path: '/:filter?',
      component: Notes,
      name: 'notes',
      meta: {
        title: 'Notes'
      },
      props: true
    }
  ]
})

router.beforeEach((to, from, next) => {
  // VALIDATE PARAMS FOR NOTES FILTERS

  if (to.name === 'notes') {
    const filter = to.params?.filter
    const valid = !filter || (['archive', 'trash'].indexOf(filter) !== -1)

    if (!valid) {
      next('/')
    }

    next()
  }
})

export default router
