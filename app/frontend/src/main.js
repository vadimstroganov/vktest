import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueSwal from 'vue-swal'

Vue.config.productionTip = false
axios.defaults.baseURL = process.env.NODE_ENV === 'development' ? 'http://localhost:8888/' : 'http://api.vk.devdev.space/'

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')

Vue.use(VueAxios, axios)
Vue.use(VueSwal)
