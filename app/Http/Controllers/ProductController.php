<?php

namespace App\Http\Controllers;

use App\Producturl;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function index()
    {
        $title = 'products';
        $products = Producturl::latest()->paginate(100);

        return view('product', compact('title', 'products'));
    }
}
