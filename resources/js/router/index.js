import Vue from 'vue'
import VueRouter from 'vue-router'

import MainNav from '../layouts/MainNav.vue'
import Notes from '../pages/Notes.vue'
import AuthLogin from '../pages/AuthLogin.vue'
import AuthRegister from '../pages/AuthRegister.vue'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: '/',
  routes: [
    {
      path: '/login',
      name: 'auth.login',
      components: {
        default: AuthLogin
      },
      meta: {
        title: 'Login',
        guest: true
      }
    },
    {
      path: '/register',
      name: 'auth.register',
      components: {
        default: AuthRegister
      },
      meta: {
        title: 'Register',
        guest: true
      }
    },
    {
      path: '/:filter?',
      components: {
        navigation: MainNav,
        default: Notes
      },
      name: 'notes',
      meta: {
        title: 'Notes',
        auth: true
      },
      props: {
        default: true
      }
    }
  ]
})

router.beforeEach((to, from, next) => {
  const userToken = localStorage.getItem('notes-app-token')

  if (to.matched.some(record => record.meta.auth)) {
    if (!userToken) {
      next({
        name: 'auth.login'
      })
    }

    // VALIDATE PARAMS FOR NOTES FILTERS
    if (to.name === 'notes') {
      const filter = to.params?.filter
      const valid = !filter || (['archive', 'trash'].indexOf(filter) !== -1)

      if (!valid) {
        next('/')
      }

      next()
    }

    next()
  } else if (to.matched.some(record => record.meta.guest)) {
    if (userToken) {
      next({
        name: 'notes'
      })
    }
    next()
  }

  next()
})

export default router
