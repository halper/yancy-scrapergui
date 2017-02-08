<?php

namespace App\Http\Controllers;

use App\Producturl;
use App\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    //
    public function index()
    {
        $title = 'sites';
        return view('site', compact('title'));
    }

    public function fetch()
    {
        $my_arr = [];
        foreach(Site::all() as $site){
            $hostname = preg_replace('/htt.*\/\//i', '', $site->url);
            $count = count(Producturl::where('url', 'like', "%$hostname%")->get());
            array_push($my_arr, [
                'id' => $site->id,
                'site' => $site->url,
                'count' => $count
            ]);
        }
        return response()->json($my_arr);
    }

    public function save(Request $request)
    {
        $count = count(Site::all());
        if($count >= 50)
            return response('Cannot parse more than 50 sites!', 500);
        $site = $request->site;
        if(strpos($site, 'http://') !== false && strpos($site, 'https://') !== false)
            return response('Given URL is not formatted properly! Must contain http:// or https://');
        Site::create(['url' => $request->site]);
        return response('success', 200);
    }

    public function delete(Request $request)
    {
        Site::find($request->sid)->delete();
        return response('success', 200);
    }
}
