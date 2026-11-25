import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../views/HomePage.vue";
import LoginPage from "../views/LoginPage.vue";
import {
  getToken,
  getUser,
  isAuthenticated,
  validateToken,
} from "../services/auth.js";
import { setAuthToken } from "../services/api.js";
import ProductPage from "../views/ProductPage.vue";
import CustomerPage from "../views/CustomerPage.vue";
import OrderPage from "../views/OrderPage.vue";
import MainLayout from "../views/layout/MainLayout.vue";
import BaseLayout from "../views/layout/BaseLayout.vue";
import OrderCreate from "../views/OrderCreate.vue";
import OrderDetailPage from "../views/OrderDetailPage.vue";

const routes = [
  {
    path: "/",
    component: MainLayout,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: HomePage,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "products",
        name: "products",
        component: ProductPage,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "customers",
        name: "customers",
        component: CustomerPage,
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: "/orders",
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: "",
            name: "orders",
            component: OrderPage,
            meta: {
              breadcrumb: "Orders",
            },
          },
          {
            path: ":id",
            name: "order-detail",
            component: OrderDetailPage,
            meta: {
              breadcrumb: "Order Detail",
            },
          },
          {
            path: "create",
            name: "order-create",
            component: OrderCreate,
            meta: {
              breadcrumb: "Create Order",
            },
          },
        ],
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

router.beforeEach(async (to) => {
  const token = getToken();

  if (to.meta.requiresAuth && !token) {
    return { name: "login" };
  }

  if (to.name === "login" && token) {
    const authenticated = await validateToken();

    if (authenticated) {
      return { name: "dashboard" };
    }
  }

  return true;
});
export default router;
