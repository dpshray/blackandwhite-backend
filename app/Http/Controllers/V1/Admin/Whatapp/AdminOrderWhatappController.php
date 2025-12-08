<?php

namespace App\Http\Controllers\V1\Admin\Whatapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Whatapp\WhatappOrderRequest;
use App\Models\WhatappOrder;
use App\ResponseTrait;
use Illuminate\Http\Request;

class AdminOrderWhatappController extends Controller
{
    //
    use ResponseTrait;
    function store(WhatappOrderRequest $request)
    {
        $data=$request->validated();
        $whatapporder=WhatappOrder::create($data);
        return $this->apiSuccess('Whatapp order created successfully', $whatapporder);
    }
}
