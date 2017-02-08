@extends('layouts.app')
@section('specific-js')
    <script src="/js/home.js"></script>
@endsection

@section('content-header')
    <h1>
        Home
        <small>Dashboard</small>
    </h1>
@endsection

@section('content')
    <div class="container" id="app">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="ion ion-bag"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Sites</span>
                        <span class="info-box-number">{{count(App\Site::all())}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="ion ion-ios-color-filter-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Filters</span>
                        <span class="info-box-number">{{count(App\Filter::all())}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="ion ion-ios-pricetags-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Products</span>
                        <span class="info-box-number">{{count(App\Producturl::all())}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recently Added Products</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ul class="products-list product-list-in-box">
                    @foreach(App\Producturl::latest('created_at')->limit(5)->get() as $product)
                        <li class="item">
                            <div class="product-img">
                                <img src="{{$product->imageLoc}}" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="{{$product->url}}" class="product-title" target="_blank">{{$product->url}}</a>

                        <span class="product-description">
                          <b>Last Mod:</b> {{$product->lastMod}}
                        </span>
                            </div>
                        </li>
                        @endforeach
                                <!-- /.item -->
                </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
                <a href="/products" class="uppercase">View All Products</a>
            </div>
            <!-- /.box-footer -->
        </div>

        <div class="row">
            <vue-alert></vue-alert>

            <vue-filter></vue-filter>
            <vue-password></vue-password>
        </div>

    </div>
@endsection
