@extends('layouts.app')

@section('content-header')
    <h1>
        Notifier Properties
    </h1>
@endsection

@section('content')
    <div class="container" id="app">
        @if(Session::has('flash_message') )

            <div class="alert alert-success fade in alert-box">

                    <a href="#" class="close" data-dismiss="alert"><i class="fa fa-times"></i></a>

                <p>Successfully saved!</p>
            </div>
        @endif
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Change Properties File</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form class="form-horizontal" action="/notifier/save" method="post">
                    {{csrf_field()}}
                    <legend>Twitter</legend>
                    <div class="form-group">
                        <label for="consumer-key" class="col-sm-2 control-label">Consumer Key</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="consumer-key"
                                   id="consumer-key" value="{{$notifier->consumer_key}}" placeholder="Consumer Key">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="consumer-secret" class="col-sm-2 control-label">Consumer Secret</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="consumer-secret"
                                   id="consumer-secret" value="{{$notifier->consumer_secret}}" placeholder="Consumer Secret">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="access-token" class="col-sm-2 control-label">Access Token</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="access-token"
                                   id="access-token" value="{{$notifier->access_token}}" placeholder="Access Token">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="token-secret" class="col-sm-2 control-label">Access Token Secret</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="token-secret"
                                   id="token-secret" value="{{$notifier->token_secret}}" placeholder="Access Token Secret">
                        </div>
                    </div>
                    <legend>GMail</legend>
                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label">Username (No @ sign)</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="username"
                                   id="username" value="{{$notifier->gmail_username}}" placeholder="GMail Username (No @ sign)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">Password</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="password"
                                   id="password" value="{{$notifier->gmail_password}}" placeholder="GMail Password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-4">
                            <button class="btn btn-block btn-flat btn-info">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">

            </div>
            <!-- /.box-footer -->
        </div>


    </div>
@endsection
