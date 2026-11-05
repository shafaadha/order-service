import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../views/HomePage.vue";
import LoginPage from "../views/LoginPage.vue";
import { getToken, getUser, isAuthenticated } from "../services/auth.js";
import { setAuthToken } from "../services/api.js";
import ProductPage from "../views/ProductPage.vue";
import CustomerPage from "../views/CustomerPage.vue";
import OrderPage from "../views/OrderPage.vue";
import MainLayout from "../views/layout/MainLayout.vue";
import BaseLayout from "../views/layout/BaseLayout.vue";

const routes = [
  {
    path: "/",
    component: MainLayout,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: "",
        name: "home",
        component: HomePage,
      },
      {
        path: "/products",
        name: "products",
        component: ProductPage,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "/customers",
        name: "customers",
        component: CustomerPage,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "/orders",
        name: "orders",
        component: OrderPage,
        meta: {
          requiresAuth: true,
        },
      },
    ],
  },
  {
    path: "/login",
    component: BaseLayout,
    children: [
      {
        path: "",
        name: "login",
        component: LoginPage,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const token = getToken();
  const authenticated = isAuthenticated();

  setAuthToken(token);

  if (to.meta.requiresAuth && !authenticated) {
    return { name: "login" };
  }

  if (to.name === "login" && authenticated) {
    return { name: "home" };
  }

  return true;
});
export default router;
