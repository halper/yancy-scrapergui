import eventHub from './events/hub'

Vue.component('proxy', require('./components/Proxy.vue'));
Vue.component('vue-alert', require('./components/Vue-Alert.vue'));
Vue.component('new-proxy', require('./components/New-Proxy.vue'));

const $proxyApp = new Vue({
    el: '#app',
});