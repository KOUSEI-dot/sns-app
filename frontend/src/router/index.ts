import { createRouter, createWebHistory } from "vue-router";
import Home from "@/views/index.vue";
import PostDetail from "@/views/PostDetail.vue"; // ← 修正！
import Login from "@/views/Login.vue";
import RegisterView from "@/views/RegisterView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: "/", name: "home", component: Home },
    { path: "/login", name: "login", component: Login },
    { path: "/register", name: "register", component: RegisterView },
    { path: "/posts/:id", name: "post-detail", component: PostDetail }, // ← OK！
  ],
});

export default router;
