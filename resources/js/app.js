require("./bootstrap");

window.CSRF = document
  .querySelector('meta[name="csrf-token"]')
  .getAttribute("content");

import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap/dist/js/bootstrap.min.js";

import Vuelidate from "vuelidate";
import Vue from "vue";
import store from "./components/store";
import App from "./components/App.vue";
import PrimaryLayout from "./components/layout/PrimaryLayout/PrimaryLayout.vue";
import TopNavigation from "./components/layout/TopNavigation.vue";
import router from "./components/helpers/router";
import VueProgressBar from 'vue-progressbar';
import VueSweetalert2 from 'vue-sweetalert2';

// Languages
import VueI18n from 'vue-i18n'
import messages from './i18n'

import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'

import 'sweetalert2/dist/sweetalert2.min.css';

window.Vue = Vue;

Vue.component('pagination', require('laravel-vue-pagination'));
Vue.use(Vuelidate);
Vue.use(VueProgressBar, {
  color: '#007bff',
  failedColor: 'red',
  thickness: '5px',
  position: 'absolute'
});
Vue.use(VueSweetalert2);
Vue.use(VueI18n)

import VueYoutube from 'vue-youtube'
Vue.use(VueYoutube)

// Infinite scroll
Vue.component('InfiniteLoading', require('vue-infinite-loading'));

import "./components";
import im from "inputmask";

Vue.directive("input-mask", {
  bind: el => {
    im().mask(el);
  },
  unbind: el => {
    im.remove(el);
  }
});

Vue.directive('click-outside', {
  bind: function (el, binding, vNode) {
    el.__vueClickOutside__ = event => {
      if (!el.contains(event.target)) {
        // call method provided in v-click-outside value
        vNode.context[binding.expression](event)
        event.stopPropagation()
      }
    }
    document.body.addEventListener('click', el.__vueClickOutside__)
  },
  unbind: function (el, binding, vNode) {
    // Remove Event Listeners
    document.body.removeEventListener('click', el.__vueClickOutside__)
    el.__vueClickOutside__ = null
  }
});

const i18n = new VueI18n({
  locale: 'en',
  fallbackLocale: 'en',
  messages,
})

new Vue({
  el: "#app",
  i18n,
  store,
  components: { App, PrimaryLayout, TopNavigation },
  router
});
