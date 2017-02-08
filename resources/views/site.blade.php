@extends('layouts.app')
@section('specific-js')
    <script src="/js/site.js"></script>
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
            <new-site></new-site>
        </div>
        <div class="row">
            <site></site>
        </div>

    </div>
@endsection
