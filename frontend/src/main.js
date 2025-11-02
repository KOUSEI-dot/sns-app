import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import api from './plugins/axios'
import './assets/css/tailwind.css'

const app = createApp(App)

// グローバル登録
app.config.globalProperties.$api = api

app.use(router)
app.mount('#app')
