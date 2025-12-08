<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductStock\StockAlertsResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use App\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    use ResponseTrait;
    function total_order()
    {
        $total_order = Order::count();
        $total_pending = Order::where('status', 'Pending')->count();
        $total_completed = Order::where('status', 'Delivered')->count();
        $total_canceled = Order::where('status', 'Cancelled')->count();
        return $this->apiSuccess('order detail', [
            'total_order' => $total_order,
            'total_pending' => $total_pending,
            'total_completed' => $total_completed,
            'total_canceled' => $total_canceled,
        ]);
    }

    public function total_revenue()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfMonth = Carbon::now()->startOfMonth();

        // Get all completed orders
        $orders = Order::where('status', 'Delivered')->get();

        $todayRevenue = 0;
        $weekRevenue = 0;
        $monthRevenue = 0;
        $totalRevenue = 0;

        foreach ($orders as $order) {
            $createdAt = Carbon::parse($order->created_at);

            $totalRevenue += $order->total_amount;

            if ($createdAt->isToday()) {
                $todayRevenue += $order->total_amount;
            }

            if ($createdAt->greaterThanOrEqualTo($startOfWeek)) {
                $weekRevenue += $order->total_amount;
            }

            if ($createdAt->greaterThanOrEqualTo($startOfMonth)) {
                $monthRevenue += $order->total_amount;
            }
        }

        return $this->apiSuccess('Revenue Detail', [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'this_week_revenue' => $weekRevenue,
            'this_month_revenue' => $monthRevenue,
        ]);
    }
    function stock_alerts()
    {
        $products = Product::with(['variants' => function ($query) {
            $query->where('stock', '<=', 5);
        }])
        ->whereHas('variants', function ($query) {
            $query->where('stock', '<=', 5);
        })
        ->get();
        if (!$products) {
            return $this->apiError('No stock alerts');
        }
        return $this->apiSuccess('stock alerts', StockAlertsResource::collection($products));
    }
    function total_user()
    {
        // Total non-admin users
        $total_user = User::where('is_admin', 0)->count();

        // Active customers: token used/created within last 30 days
        $active_customer = User::where('is_admin', 0)
            ->whereHas('tokens', function ($query) {
                $query->where('updated_at', '>=', now()->subDays(30));
            })
            ->count();

        // Inactive customers: no token or token not used in last 30 days
        $inactive_customer = User::where('is_admin', 0)
            ->whereDoesntHave('tokens')
            ->orWhereHas('tokens', function ($query) {
                $query->where('updated_at', '<', now()->subDays(30));
            })
            ->count();

        // New customers: created within last 30 days
        $new_customer = User::where('is_admin', 0)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        return $this->apiSuccess('customer Detail', [
            'total_user'        => $total_user,
            'active_customer'   => $active_customer,
            'inactive_customer' => $inactive_customer,
            'new_customer'      => $new_customer
        ]);
    }
    function total_product()
    {
        $products = Product::all();
        $total_product = Product::count();
        $variants= Variant::all();
        $in_stock = 0;
        $out_stock = 0;
        $low_stock = 0;

        foreach ($variants as $variant) {
            if ($variant->stock > 5) {
                $in_stock++;
            } elseif ($variant->stock > 0 && $variant->stock <= 5) {
                $low_stock++;
            } else {
                $out_stock++;
            }
        }
        return $this->apiSuccess('product Detail', [
            'total' => $total_product,
            'in_stock' => $in_stock,
            'low_stock' => $low_stock,
            'out_stock' => $out_stock
        ]);
    }
    function best_seller()
    {
        $bestSellers = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_sold')
        )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(10) // top 10
            ->get();
        if (!$bestSellers) {
            return $this->apiError('no best seller product found');
        }
        return response()->json([
            'status' => 'success',
            'data' => $bestSellers
        ]);
    }
    public function line_chart()
    {
        // Group orders by date and sum their total_amount
        $sales = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total_sales')
            ->where('status', '!=', 'Cancelled') // optional: ignore cancelled orders
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format response for chart
        $data = [
            'labels' => $sales->pluck('date'),          // x-axis (dates)
            'sales'  => $sales->pluck('total_sales'),   // y-axis (sales)
        ];

        return response()->json([
            'message' => 'Daily sales data',
            'data'    => $data,
            'success' => true
        ]);
    }
}
