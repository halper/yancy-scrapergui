import eventHub from './events/hub'

Vue.component('site', require('./components/Site.vue'));
Vue.component('vue-alert', require('./components/Vue-Alert.vue'));
Vue.component('new-site', require('./components/New-Site.vue'));

const $siteApp = new Vue({
    el: '#app',
});