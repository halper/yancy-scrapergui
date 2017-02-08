import eventHub from './events/hub'

Vue.component('vue-alert', require('./components/Vue-Alert.vue'));
Vue.component('vue-filter', require('./components/Filter.vue'));
Vue.component('vue-password', require('./components/Password.vue'));

const $homeApp = new Vue({
    el: '#app',
});