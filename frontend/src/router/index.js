import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../views/HomePage.vue";
import LoginPage from "../views/LoginPage.vue";
import { getToken, getUser, isAuthenticated } from "../services/auth.js";
import { setAuthToken } from "../services/api.js";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomePage,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/login",
    name: "login",
    component: LoginPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const token = getToken();

  if (token) {
    setAuthToken(token);
  } else {
    setAuthToken(null);
  }

  if (to.meta.requiresAuth && !isAuthenticated()) {
    return { name: "login" };
  }

  if (to.name === "login" && isAuthenticated()) {
    return { name: "home" };
  }

  return true;
});

export default router;
