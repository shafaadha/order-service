import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import router from "../src/router/index.js";
import { setAuthToken } from "./services/api.js";

const app = createApp(App);
app.use(router);
const token = localStorage.getItem("token");
if (token) {
  setAuthToken(token);
}

app.mount("#app");
