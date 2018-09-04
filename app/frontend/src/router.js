import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'index',
      component: () => import('./views/ItemList.vue')
    },
    {
      path: '/items/new',
      name: 'addItem',
      component: () => import('./views/ItemAdd.vue')
    },
    {
      path: '/items/:id/edit',
      name: 'editItem',
      component: () => import('./views/ItemEdit.vue')
    }
  ]
})
