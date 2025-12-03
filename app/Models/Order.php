<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id',
        'billing_information_id',
        'total_amount',
        'status',
        'address_details',
        'payment_status',
    ];
    protected $casts = [
        'address_details' => 'array',
        'total_amount' => 'decimal:2',
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function billingInformation()
    {
        return $this->belongsTo(BillingInformation::class, 'billing_information_id');
    }
}
