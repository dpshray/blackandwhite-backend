<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\AdminOrderRequest;
use App\Http\Resources\Order\AdminOrderDetailCollection;
use App\Models\Order;
use App\Models\Product;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    function add_order(AdminOrderRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 0,
            'status' => 'Pending',
            'billing_information_id' => null,
            'address_details' => [
                'first_name'     => $data['name'],
                'address'        => $data['address'],
                'contact_number' => $data['phone'],
            ],
            'payment_status' => 'unpaid',
        ]);
        $product = Product::findOrFail($data['product_id']);
        $subtotal = 0;
        $price = $product->discount_price ?? $product->price;
        $lineTotal = $price * $data['quantity'];
        $subtotal += $lineTotal;
        $order->orderItems()->create([
            'product_id' => $data['product_id'],
            'variant_id' => null,
            'quantity' => $data['quantity'],
            'size' => $data['size'],
            'color' => $data['color'],
            'product_name' => $product->name,
            'total_amount' => $lineTotal,
        ]);
        $order->update([
            'total_amount' => $subtotal ,
        ]);
        return $this->apiSuccess('order save successfull');
    }
}
