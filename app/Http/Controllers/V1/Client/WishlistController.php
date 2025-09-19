<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wishlist\WishlistResource;
use App\Models\Variant;
use App\Models\Wishlist;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    //
    use ResponseTrait;
    function add_favorites(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'product_id'=>'required|exists:products,id',
            'variant_id'=>'sometimes|exists:variants,id',
        ]);
        // If no variant_id is provided, use the first variant of the product
        $variantId = $request->variant_id;

        if (!$variantId) {
            $variant = Variant::where('product_id', $request->product_id)->first();
            if (!$variant) {
                return $this->apiError('No variant found for this product');
            }
            $variantId = $variant->id;
        }

        // Check if already favorited
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $variantId)
            ->exists();

        if ($exists) {
            return $this->apiError('Already added to favorites');
        }

        $favorites = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'variant_id' => $variantId,
        ]);

        if (!$favorites) {
            return $this->apiError('Failed to add to favorites');
        }

        return $this->apiSuccess('Successfully added to wishlist', null);
    }

    function remove_favorites(Wishlist $wishlist)
    {
        $user = Auth::user();
        $wishlist->delete();
        return $this->apiSuccess('Successfull removed favorites', null);
    }
    function view_favorites()
    {
        $user = Auth::user();
        $favorites = Wishlist::with('product', 'variant','categories')->where('user_id', $user->id)->get();
        // $totalfavorites = Favorites::where('user_id', $user->id)->count();
        if (!$favorites) {
            return $this->apiError('favorites not found ');
        }
        $count = $favorites->count();

        return $this->apiSuccess('Favourites list', [
            'total_favourites' => $count,
            'favourites' => WishlistResource::collection($favorites),
        ]);
    }
}
