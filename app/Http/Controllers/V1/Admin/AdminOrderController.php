<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\AdminOrderDetailCollection;
use App\Models\Order;
use App\ResponseTrait;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    //
    use ResponseTrait;
    function all_order()
    {
        $orders = Order::with([
            'items.product',
            'items.variant',
            'user',
            'billingInformation'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        if ($orders->isEmpty()) {
            return $this->apiError('No order was found');
        }

        $orders = new AdminOrderDetailCollection($orders);
        return $this->apiSuccess('User order', $orders);
    }
    function update_order(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Shipped,Delivered,Processing,Cancelled',
        ]);
        $updated = $order->update([
            'status' => $request->status
        ]);
        if (!$updated) {
            return $this->apiError('Failed to update status');
        }
        return $this->apiSuccess('status has been updated');
    }
}
