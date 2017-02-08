<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //

    public static function getFileProperties()
    {
        $dir = '../logs/';
        $files = array_diff(scandir($dir), array('..', '.'));
        $log_files = [];
        foreach ($files as $file) {
            $log_files[] = [
                'name' => $file,
                'size' => round(filesize($dir . $file) / 100) /10
            ];
        }
        return $log_files;
    }
}
