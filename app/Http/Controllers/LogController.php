<?php

namespace App\Http\Controllers;

use App\Log;

class LogController extends Controller
{
    //

    public function index()
    {
        $title = 'logs';
        $log_files = Log::getFileProperties();
        return view('logs', compact('title', 'log_files'));
    }
}
