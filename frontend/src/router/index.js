import { createRouter, createWebHistory } from 'vue-router'
import PageHome from "@/features/PageHome.vue"
import PageList from "@/features/PageList.vue"
import PageForm from "@/features/PageForm.vue"
import PageAdmin from "@/features/PageAdmin.vue"

const routes = [
  {path: '/', component: PageHome,},
  {path: '/list', component: PageList,},
  {path: '/form', component: PageForm,},
  {path: '/admin', component: PageAdmin,},
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
