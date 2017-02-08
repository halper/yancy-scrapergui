@extends('layouts.app')

@section('content-header')
    <h1>
        Products
    </h1>
@endsection

@section('content')
    <div class="container" id="app">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Products</h3>

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
                    @foreach($products as $product)
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
                {{$products->links()}}
            </div>
            <!-- /.box-footer -->
        </div>



    </div>
@endsection
