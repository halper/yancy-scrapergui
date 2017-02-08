<?php

namespace App\Http\Controllers;

use App\Filter;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    //
    public function fetch()
    {
        return response()->json(Filter::all());
    }

    public function save(Request $request)
    {
        $filters = preg_replace('/\s+/i', '', $request->newFilters);
        $filters = explode(';', $filters);
        foreach($filters as $filter){
            Filter::create(['keyword' => $filter]);
        }
        return response('success', 200);
    }

    public function update(Request $request)
    {
        $posted_filters = $request->filters;
        $filters = Filter::all();
        foreach ($filters as $filter){
            $is_in_posted_filters = false;
            foreach($posted_filters as $filtered_to_be_compared){
                if($filtered_to_be_compared['id'] == $filter->id){
                    $is_in_posted_filters = true;
                    break;
                }
            }
            if(!$is_in_posted_filters){
                $filter->delete();
            }
        }
        return response('success', 200);
    }
}
