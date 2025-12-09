<?php

namespace App\Http\Controllers\V1\Admin\Whatapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Whatapp\WhatappOrderRequest;
use App\Http\Resources\Admin\WhatAppOrder\AdminWhatAppOrderCollection;
use App\Http\Resources\Admin\WhatAppOrder\AdminWhatAppOrderResource;
use App\Models\WhatappsOrder;
use App\ResponseTrait;
use Illuminate\Http\Request;

class AdminOrderWhatappController extends Controller
{
    //
    use ResponseTrait;
    function store(WhatappOrderRequest $request)
    {
        $data=$request->validated();
        $data['order_status']='pending';
        $whatapporder=WhatappsOrder::create($data);
        return $this->apiSuccess('Whatapp order created successfully', $whatapporder);
    }
    function index(Request $request)
    {
        $limit =$request->per_page ?? 10;
        $orders=WhatappsOrder::orderBy('created_at', 'desc')->paginate($limit);
        $orders= new AdminWhatAppOrderCollection($orders);
        return $this->apiSuccess('Whatapp orders fetched successfully', $orders);
    }
    function update(Request $request, WhatappsOrder $whatappsOrder)
    {
        $request->validate([
            'status'=>'required|string',
        ]);
        $whatappsOrder->order_status=$request->status;
        $whatappsOrder->save();
        return $this->apiSuccess('Whatapp order updated successfully', new AdminWhatAppOrderResource($whatappsOrder));
    }
}
