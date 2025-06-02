import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import "./index.css";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";

const app = createApp(App);
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);
app.use(router);
app.use(Vue3Toastify, {
  autoClose: 1600,
  position: "top-center",
  theme: "light",
});
app.mount("#app");
