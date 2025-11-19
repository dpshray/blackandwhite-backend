<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutSession extends Model
{
    //
    protected $casts=[
        'user_id'=>'integer',
        'product_id'=>'integer',
        'variant_id'=>'integer',
        'quantity'=>'integer',
    ];
    protected $fillable=[
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
        'expires_at'
    ];
}
