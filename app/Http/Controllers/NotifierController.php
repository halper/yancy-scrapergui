<?php

namespace App\Http\Controllers;

use App\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotifierController extends Controller
{
    //
    public function index()
    {
        $notifier = new Notifier();
        $title = 'notifier';
        return view('notifier-properties', compact('title', 'notifier'));
    }

    public function save(Request $request)
    {
        Notifier::saveNewProps($request->all());
        Session::flash('flash_message', 'Success');
        return redirect()->back();
    }
}
