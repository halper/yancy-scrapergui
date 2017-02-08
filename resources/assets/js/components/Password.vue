<template>
    <div class="col-xs-12 col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Change Password</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="password">Password</label>
                        <input class="form-control" type="password" name="password" id="password"
                               v-model="password">
                    </div>
                    <div class="col-sm-12">
                        <label for="confirmation">Confirm</label>
                        <input class="form-control" type="password" name="confirmation" id="confirmation"
                               v-model="confirmation">
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-offset-4 col-sm-4">
                        <button class="btn btn-block btn-flat btn-primary" v-on:click="changePassword()">Change
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</template>

<script>
    import eventHub from '../events/hub'
    export default{
        data: function () {
            return {
                password: '',
                confirmation: '',
            }
        },
        methods: {
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            changePassword: function () {
                this.resetNotifications();
                if (this.password === "") {
                    this.showNotification('error', 'Password cannot be empty!');
                    return;
                } else if (this.confirmation !== this.password) {
                    this.showNotification('error', 'Password and confirmation do not match!');
                    return;
                }
                this.$http.post('/user/change-password', {
                    password: this.password
                }).then(function () {
                    this.showNotification('s', 'Your password has changed successfully!');
                }, function (response) {
                    this.showNotification('error', 'Something went wrong: ' + response.data);
                });
            },
            showNotification: function (type, message) {
                eventHub.$emit('show-notification', type, message);
            },
        }
    }
</script>
