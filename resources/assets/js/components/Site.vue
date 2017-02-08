<template>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Sites</h3>

            <div class="box-tools pull-right" style="width: 300px">
                <input type="text" v-model="search" class="input-sm form-control" placeholder="Search for site or count">
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>ID</th>
                    <th>Site</th>
                    <th># of Products</th>
                    <th>Actions</th>
                </tr>
                <tr v-for="(site, index) in filteredSites">
                    <td>{{index+1}}</td>
                    <td><a :href="site.site + '/sitemap_products_1.xml'" target="_blank">{{site.site}}</a></td>
                    <td>{{site.count}}</td>
                    <td>
                        <button class="btn btn-xs btn-flat btn-danger" @click="deleteSite(site)">Delete</button>
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
                sites: [],
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
                this.$http.get('/sites/fetch').then(function (response) {
                    this.sites = response.data;
                })
            },
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            showNotification: function (type, message) {
                eventHub.$emit('show-notification', type, message);
            },
            deleteSite: function (site) {
                this.$http.post('/sites/delete', {
                    sid: site.id
                }).then(function () {
                    this.showNotification('s', site.site + ' has been removed!');
                    this.fetch();
                    eventHub.$emit('delete', site.site);
                }, function (response) {
                    this.showNotification('error', 'Something went wrong: ' + response.data);
                })
            }
        },
        computed : {
            filteredSites: function(){
                var self = this;
                return self.sites.filter(function (site) {
                    return site.site.indexOf(self.search) !== -1 || site.count == self.search
                })
            }
        }
    }
</script>
