<template>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Proxies</h3>

            <div class="box-tools pull-right" style="width: 300px">
                <input type="text" v-model="search" class="input-sm form-control" placeholder="Search for proxy">
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>ID</th>
                    <th>Host</th>
                    <th>Port</th>
                    <th>Actions</th>
                </tr>
                <tr v-for="(proxy, index) in filteredProxies">
                    <td>{{index+1}}</td>
                    <td>{{proxy.host}}</td>
                    <td>{{proxy.port}}</td>
                    <td>
                        <button class="btn btn-xs btn-flat btn-danger" @click="deleteProxy(proxy)">Delete</button>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer text-center">

        </div>
        <!-- /.box-footer -->
    </div>
</template>

<script>
    import eventHub from '../events/hub'

    export default{
        data: function () {
            return {
                proxies: [],
                search: '',
            }
        },
        mounted: function () {
            this.fetch();
        }, created: function () {
            eventHub.$on('fetch', this.fetch);
        },
        beforeDestroy: function () {
            eventHub.$off('fetch', this.fetch);
        },
        methods: {
            fetch: function () {
                this.$http.get('/proxies/fetch').then(function (response) {
                    this.proxies = response.data;
                })
            },
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            showNotification: function (type, message) {
                eventHub.$emit('show-notification', type, message);
            },
            deleteProxy: function (proxy) {
                this.$http.post('/proxies/delete', {
                    pid: proxy.id
                }).then(function () {
                    this.showNotification('s', proxy.host + ' has been removed!');
                    this.fetch();
                    eventHub.$emit('delete', proxy);
                }, function (response) {
                    this.showNotification('error', 'Something went wrong: ' + response.data);
                })
            }
        },
        computed : {
            filteredProxies: function(){
                var self = this;
                return self.proxies.filter(function (proxy) {
                    return proxy.host.indexOf(self.search) !== -1
                })
            }
        }
    }
</script>
