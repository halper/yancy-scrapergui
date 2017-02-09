<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = ["host", "port"];
}
