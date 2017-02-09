<template>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Add New Proxy</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="form-group">
                    <label for="host" class="control-label col-sm-1">Host</label>

                    <div class="col-sm-4">
                        <input class="form-control" type="text" name="host" id="host"
                               v-model="host" placeholder="http(s)://example.com">
                    </div>
                    <label for="port" class="control-label col-sm-1">Port</label>

                    <div class="col-sm-4">
                        <input class="form-control" type="number" name="port" id="port"
                               v-model="port" placeholder="8080">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-flat btn-success" @click="addProxy()">Save
                        </button>
                    </div>
                </div>

            </div>

        </div>
        <!-- /.box-body -->
    </div>

</template>
<script>
    import eventHub from '../events/hub'

    export default{
        data: function () {
            return {
                host: '',
                port: ''
            }
        },
        created: function () {
            eventHub.$on('delete', this.setProxyInputs);
        },
        beforeDestroy: function () {
            eventHub.$off('delete', this.setProxyInputs);
        },
        methods: {
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            setProxyInputs: function (proxy) {
                this.host = proxy.host;
                this.port = proxy.port;
            },
            addProxy: function () {
                this.resetNotifications();
                if (this.host === "") {
                    this.showNotification('error', 'Host cannot be empty!');
                    return;
                }
                if (this.port === "") {
                    this.showNotification('error', 'Port cannot be empty!');
                    return;
                }
                this.$http.post('/proxies/save', {
                    host: this.host,
                    port: this.port
                }).then(function () {
                    this.showNotification('s', this.host + ' has been saved!');
                    this.host = '';
                    this.port = '';
                    eventHub.$emit('fetch');
                }, function (response) {
                    this.showNotification('error', 'Something went wrong: ' + response.data);
                })
            },
            showNotification: function (type, message) {
                eventHub.$emit('show-notification', type, message);
            },
        }
    }
</script>
