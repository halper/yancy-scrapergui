<?php

namespace App\Http\Controllers;

use App\Proxy;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function index()
    {
        $title = 'proxies';
        return view('proxy', compact('title'));
    }

    public function fetch()
    {
        return response()->json(Proxy::all());
    }

    public function save(Request $request)
    {
        $host = $request->host;
        if(strpos($host, 'http://') !== false && strpos($host, 'https://') !== false)
            return response('Given URL is not formatted properly! Must contain http:// or https://');
        Proxy::create(['host' => $request->host, 'port' => $request->port]);
        return response('success', 200);
    }

    public function delete(Request $request)
    {
        Proxy::find($request->pid)->delete();
        return response('success', 200);
    }
}
