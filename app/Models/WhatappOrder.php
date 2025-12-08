<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatappOrder extends Model
{
    //
    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'product_id',
        'size',
        'color',
        'date',
    ];
}
