<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInformation extends Model
{
    //
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'state',
        'city',
        'address',
        'contact_number',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
