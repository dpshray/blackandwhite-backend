<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatappsOrder extends Model
{
    //
    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'product_code',
        'size',
        'color',
        'date',
        'order_status',
    ];
}
