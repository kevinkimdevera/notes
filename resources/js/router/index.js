import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Notes from '../pages/Notes.vue'
import Trash from '../pages/Trash.vue'
import Archive from '../pages/Archive.vue'

const router = new VueRouter({
  mode: 'history',
  base: '/',
  routes: [
    {
      path: '/',
      component: Notes,
      name: 'notes.index',
      meta: {
        title: 'My Notes'
      }
    },
    {
      path: '/archive',
      component: Archive,
      name: 'notes.archive',
      meta: {
        title: 'Archive'
      }
    },
    {
      path: '/trash',
      component: Trash,
      name: 'notes.trash',
      meta: {
        title: 'Trash'
      }
    },
  ]
})

export default router
