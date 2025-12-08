<?php

namespace App\Http\Controllers\V1\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\ResponseTrait;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    //
    use ResponseTrait;
    function deliveryCharge(Request $request)
    {
        //
        $setting=Setting::updateOrCreate(
            ['key' => 'delivery_charge'],
            ['value' => $request->value]
        );
        return $this->apiSuccess('Delivery charge updated successfully', $setting);
    }
    function getDeliveryCharge()
    {
        //
        $setting=Setting::where('key', 'delivery_charge')->first();
        return $this->apiSuccess('Delivery charge fetched successfully', $setting);
    }
}
