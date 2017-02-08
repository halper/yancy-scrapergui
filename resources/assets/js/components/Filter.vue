<template>
    <div class="col-xs-12 col-md-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">URL Filters</h3>

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
                    <div class="col-sm-12">
                        <p>Use semicolon to separate new filters as: nike;adidas;etc...</p>
                        <p>To remove filters uncheck related box and click 'Save'</p>
                    </div>
                    <div class="col-sm-12">
                        <label for="filter">Filters</label>
                        <input class="form-control" type="text" name="filter" id="filter"
                               v-model="filterInput">
                    </div>
                    <div class="col-sm-3" v-for="filter in filterCbs">
                        <label class="checkbox-inline">
                            <input type="checkbox" v-model="filters"
                                   v-bind:value="filter">
                            {{ filter.keyword }}
                        </label>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-offset-4 col-sm-4">
                        <button class="btn btn-block btn-flat btn-primary" v-on:click="changeFilters()">Save
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
                filterInput: '',
                filters: [],
                filterCbs: [],
            }
        },
        mounted: function () {
            this.fetch();
        },
        methods: {
            fetch: function () {
                this.resetNotifications();
                this.$http.get('/filter/fetch').then(function (response) {
                    this.filters = response.data;
                    this.filterCbs = response.data;
                })
            },
            resetNotifications: function () {
                eventHub.$emit('reset');
            },
            changeFilters: function () {
                if (this.filterInput !== "") {
                    this.insertNewFilters();
                } else {
                    this.updateFilters();
                }
                this.fetch();
            },
            insertNewFilters: function () {
                this.$http.post('/filter/save', {
                    newFilters: this.filterInput
                }).then(function () {
                    this.showNotification('s', "Filters are saved!");
                    this.filterInput = '';
                }, function (response) {
                    this.showNotification('error', "Something went wrong: " + response.data);
                })
            },
            updateFilters: function () {
                this.$http.post('/filter/update', {
                    filters: this.filters,
                }).then(function () {
                    this.showNotification('s', "Filters are updated!");
                }, function (response) {
                    this.showNotification('error', "Something went wrong: " + response.data);
                })
            },
            showNotification: function (type, message) {
                eventHub.$emit('show-notification', type, message);
            },
        }
    }
</script>
