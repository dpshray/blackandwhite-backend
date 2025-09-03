<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\RatingRequest;
use App\Models\Product;
use App\Models\Rating;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    //
    use ResponseTrait;
    function view(Product $product)
    {
        $rating=Rating::where('product_id',$product->id)->get();
        $average_rating =$rating->avg('rating');
        if(!$average_rating)
        {
            return $this->apiError('0 rating on this product');
        }
        return $this->apiSuccess('product average rating ',['average rating'=>$average_rating]);
    }
    function add(RatingRequest $request,Product $product)
    {
        $user=Auth::user();
        $rating=Rating::create([
            'user_id'=>$user->id,
            'product_id'=>$product->id,
            'rating'=>$request->rating,
            'review'=>$request->review,
        ]);
    }
}
