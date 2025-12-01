<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Cart;
use App\Models\CheckoutSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Variant;
use App\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class OrderController extends Controller
{
    //
    use ResponseTrait;
    public function add_order(Request $request, $id)
    {
        $user = Auth::user();
        $delivery_fee = 200;
        $subtotal = 0;

        try {
            DB::beginTransaction();

            if ($request->buynow == 0) {

                $cartItems = cart::where('user_id', $user->id)->get();

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'Pending',
                    'billing_information_id' => $id,
                ]);

                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    $price = $product->discount_price ?? $product->price;
                    $lineTotal = $price * $item->quantity;
                    $subtotal += $lineTotal;

                    $order->orderItems()->create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'total_amount' => $lineTotal,
                    ]);
                }

                $order->update([
                    'total_amount' => $subtotal + $delivery_fee,
                ]);

                // delete cart only in cart mode
                cart::where('user_id', $user->id)->delete();
            }

            // BUY NOW
            else if ($request->buynow == 1) {

                $checkout = CheckoutSession::where('user_id', $user->id)
                    ->latest('id')
                    ->first();

                if (!$checkout) {
                    throw new Exception("Checkout session not found.");
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'Pending',
                    'billing_information_id' => $id,
                ]);


                $product = Product::findOrFail($checkout->product_id);
                $price = $product->discount_price ?? $product->price;

                $lineTotal = $price * $checkout->quantity;
                $subtotal += $lineTotal;

                $order->orderItems()->create([
                    'product_id' => $checkout->product_id,
                    'variant_id' => $checkout->variant_id,
                    'quantity' => $checkout->quantity,
                    'total_amount' => $lineTotal,
                ]);

                $order->update([
                    'total_amount' => $subtotal + $delivery_fee,
                ]);
            }

            DB::commit();
            return $this->apiSuccess("Checkout successful. Your order has been placed.");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiError("Checkout failed: " . $e->getMessage());
        }
    }


    function history_of_order(Request $request)
    {
        $limit = $request->input('limit', 9);
        $user = Auth::user();
        $orders = Order::with(['items.product', 'items.variant'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc') // newest first
            ->paginate($limit);
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
        // $request->validate([
        //     'status' => 'required|in:Cancelled'
        // ]);
        $update = $order->update([
            'status' => 'Cancelled'
        ]);
        if (!$update) {
            return $this->apiError('Failed to update');
        }
        return $this->apiSuccess('your order has been Cancelled');
    }
    public function buyNow(Request $request)
    {
        $user = Auth::user();
        // return $request->all();
        try {
            DB::beginTransaction();
            $checkout = CheckoutSession::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                // 'expires_at' => now()->addMinutes(30),
            ]);
            // return ($checkout);
            DB::commit();
            return $this->apiSuccess("Buy Now checkout successful!", $checkout);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiError("Buy Now failed: " . $e->getMessage());
        }
    }
}
