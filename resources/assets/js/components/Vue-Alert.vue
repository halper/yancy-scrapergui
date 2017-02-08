<template>
    <div class="alert alert-dismissible"
         v-bind:class="{'alert-danger' : notificationError, 'alert-success': notificationSuccess}"
         v-show="notificationMessage">
        <button type="button" class="close"
                v-on:click="resetNotifications()">×
        </button>
        <h4><i class="icon fa" v-bind:class="{'fa-ban' : notificationError, 'fa-check': notificationSuccess}"></i> Alert!</h4>
        {{ notificationMessage }}
    </div>
</template>

<script>
    import eventHub from '../events/hub'
    export default{
        data: function () {
            return {
                notificationError: '',
                notificationSuccess: '',
                notificationMessage: ''
            }
        },
        created: function () {
            eventHub.$on('show-notification', this.showNotification);
            eventHub.$on('reset', this.resetNotifications);
        },
        beforeDestroy: function () {
            eventHub.$off('show-notification', this.showNotification);
            eventHub.$off('reset', this.resetNotifications);
        },
        methods: {

            resetNotifications: function () {
                this.notificationError = false;
                this.notificationSuccess = false;
                this.notificationMessage = false;
            },
            showNotification: function (type, message) {

                var error = false;
                if (type === 'error' || type === 'danger')
                    error = true;
                this.notificationMessage = message;
                if (error) {
                    this.notificationError = true;
                } else {
                    this.notificationSuccess = true;
                }
            }
        }
    }
</script>
