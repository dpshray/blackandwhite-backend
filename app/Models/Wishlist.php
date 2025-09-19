<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    //
    protected $fillable=[
        'user_id',
        'product_id',
        'variant_id'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
    public function categories()
    {
        return $this->belongsTo(Categories::class,'product_categories');
    }
}
