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
        $count=$rating->count();
        if(!$average_rating)
        {
            return $this->apiError('0 rating on this product');
        }
        return $this->apiSuccess('product average rating ',[
            'average rating'=>$average_rating,
            'total_rating'=>$count,
        ]);
    }
    function add(RatingRequest $request,Product $product)
    {
        $user=Auth::user();
        $exists=Rating::where('product_id',$product->id)->where('user_id',$user->id)->get();
        if($exists)
        {
            return $this->apiError('{$user->name} has already given rating on this product');
        }
        $rating=Rating::create([
            'user_id'=>$user->id,
            'product_id'=>$product->id,
            'rating'=>$request->rating,
            'review'=>$request->review,
        ]);
        if(!$rating)
        {
            return $this->apiError('failed to add rating');
        }
        return $this->apiSuccess('your rating has been added rating');
    }
}
