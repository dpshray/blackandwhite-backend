<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\AdminNotification;
use App\Models\BillingInformation;
use App\Models\Cart;
use App\Models\CheckoutSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Variant;
use App\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Collection\Set;

use function Symfony\Component\Clock\now;

class OrderController extends Controller
{
    //
    use ResponseTrait;
    public function add_order(Request $request, $id)
    {
        $user = Auth::user();
        $delivery_fee = Setting::where('key', 'delivery_charge')->value('value') ?? 0;
        $subtotal = 0;
        $addressDetails = BillingInformation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        try {
            DB::beginTransaction();

            if ($request->buynow == 0) {

                $cartItems = cart::where('user_id', $user->id)->get();

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'Pending',
                    'billing_information_id' => $id,
                    'address_details' => [
                        'first_name'     => $addressDetails->first_name,
                        'last_name'      => $addressDetails->last_name,
                        'email'          => $addressDetails->email,
                        'state'          => $addressDetails->state,
                        'city'           => $addressDetails->city,
                        'address'        => $addressDetails->address,
                        'contact_number' => $addressDetails->contact_number,
                    ],
                    'payment_status' => 'unpaid',
                ]);

                foreach ($cartItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    $variant = Variant::findOrFail($item->variant_id);
                    $price = $product->discount_price ?? $product->price;
                    $lineTotal = $price * $item->quantity;
                    $subtotal += $lineTotal;

                    $order->orderItems()->create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'product_name' => $product->name,
                        'total_amount' => $lineTotal,
                    ]);
                }

                $order->update([
                    'total_amount' => $subtotal + $delivery_fee,
                ]);
                $variant->update([
                    'stock' => $variant->stock - $item->quantity,
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
                    'address_details' => [
                        'first_name'     => $addressDetails->first_name,
                        'last_name'      => $addressDetails->last_name,
                        'email'          => $addressDetails->email,
                        'state'          => $addressDetails->state,
                        'city'           => $addressDetails->city,
                        'address'        => $addressDetails->address,
                        'contact_number' => $addressDetails->contact_number,
                    ],
                    'payment_status' => 'unpaid',
                ]);


                $product = Product::findOrFail($checkout->product_id);
                $variant = Variant::findOrFail($checkout->variant_id);
                $price = $product->discount_price ?? $product->price;

                $lineTotal = $price * $checkout->quantity;
                $subtotal += $lineTotal;

                $order->orderItems()->create([
                    'product_id' => $checkout->product_id,
                    'variant_id' => $checkout->variant_id,
                    'quantity' => $checkout->quantity,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'product_name' => $product->name,
                    'total_amount' => $lineTotal,
                ]);
                $variant->update([
                    'stock' => $variant->stock - $checkout->quantity,
                ]);
                $order->update([
                    'total_amount' => $subtotal + $delivery_fee,
                ]);
            }
            AdminNotification::create([
                'title' => 'New Order Placed',
                'message' => "Order #{$order->id} has been placed by user {$user->name}.",
                'is_read' => false,
            ]);
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
        $update = $order->update([
            'status' => 'Cancelled'
        ]);
        AdminNotification::create([
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->id} has been cancelled by user {$user->name}.",
            'is_read' => false,
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
