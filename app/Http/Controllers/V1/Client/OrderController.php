<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Variant;
use App\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    use ResponseTrait;
    public function add_order()
    {
        $lineTotal = 0;
        $subtotal = 0;
        $delivery_fee = 200;
        $user = Auth::user();
        $cart = cart::where('user_id', $user->id);
        try {
            DB::beginTransaction();
            $order = Order::create([
                'user_id'      => $user->id,
                'total_amount' => 0, // will update later
                'status'       => 'Pending',
            ]);

            foreach ($cart->get() as $item) {
                if ($item->variant_id) {
                    $variant = Variant::find($item->variant_id);
                    if (!$variant) {
                        throw new Exception("Variant not found.");
                    }
                    $price = $variant->discount_price ?? $variant->price;
                } else {
                    $product = Product::find($item->product_id);
                    if (!$product) {
                        throw new Exception("Product not found.");
                    }
                    $price = $product->discount_price ?? $product->price;
                }
                $lineTotal = $price * $item->quantity;
                $subtotal += $lineTotal;
                $order->orderItems()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                    'total_amount' => $lineTotal, // unit price
                ]);
                $total = $subtotal + $delivery_fee;
                $order->update([
                    'total_amount' => $total,
                ]);
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiError("Checkout failed: " . $e->getMessage());
        }
        $cart->delete();
        return $this->apiSuccess("checkout successfull your order has been placed");
    }


    function history_of_order()
    {
        $user = Auth::user();
        $orders = Order::with(['items.product', 'items.variant'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc') // newest first
            ->paginate(10);
        if (!$orders) {
            return $this->apiError('No order was found');
        }
        return $this->apiSuccess('User order', [
            'orders' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
            'links' => [
                'first' => $orders->url(1),
                'last'  => $orders->url($orders->lastPage()),
                'prev'  => $orders->previousPageUrl(),
                'next'  => $orders->nextPageUrl(),
            ]
        ]);
    }
    function order_edit(Request $request, Order $order)
    {
        $user = Auth::user();
        $request->validate([
            'status' => 'required|in:Cancelled'
        ]);
        $update = $order->update([
            'status' => 'Cancelled'
        ]);
        if (!$update) {
            return $this->apiError('Failed to update');
        }
        return $this->apiSuccess('your order has been Cancelled');
    }
}
