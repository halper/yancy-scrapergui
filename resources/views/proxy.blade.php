@extends('layouts.app')
@section('specific-js')
    <script src="/js/proxy.js"></script>
@endsection

@section('content-header')
    <h1>
        Sites
    </h1>
@endsection

@section('content')
    <div class="container" id="app">
        <vue-alert></vue-alert>
        <div class="row">
            <new-proxy></new-proxy>
        </div>
        <div class="row">
            <proxy></proxy>
        </div>

    </div>
@endsection
