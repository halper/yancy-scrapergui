<template>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Add New Site</h3>

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
                    <label for="new-site" class="control-label col-sm-1">URL</label>

                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="new-site" id="new-site"
                               v-model="siteInput" placeholder="http(s)://example.com">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-flat btn-success" v-on:click="addSite()">Save
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
                siteInput: '',
            }
        },
        created: function () {
            eventHub.$on('delete', this.setSiteInput);
        },
        beforeDestroy: function () {
            eventHub.$off('delete', this.setSiteInput);
        },
        methods: {
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            setSiteInput: function (site) {
                this.siteInput = site;
            },
            addSite: function () {
                this.resetNotifications();
                if (this.siteInput === "") {
                    this.showNotification('error', 'Site url cannot be empty!');
                    return;
                }
                this.$http.post('/sites/save', {
                    site: this.siteInput
                }).then(function () {
                    this.showNotification('s', this.siteInput + ' has been saved!');
                    this.siteInput = '';
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
