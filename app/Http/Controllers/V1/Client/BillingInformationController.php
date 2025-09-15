<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing_Information\InformationRequest;
use App\Http\Resources\Billing_Information\InformationResource;
use App\Models\BillingInformation;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationController extends Controller
{
    //
    use ResponseTrait;
    function store(InformationRequest $request)
    {
        $user = Auth::user();
        $address = BillingInformation::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
        ]);

        if (!$address) {
            return $this->apiError('Failed to save information');
        }
        return $this->apiSuccess('your information has been save successfull', $address);
    }
    function update(InformationRequest $request, BillingInformation $billing)
    {
        $user = Auth::user();
        $address = $billing->update($request->validated());
        if (!$address) {
            return $this->apiError('Failed to update information');
        }
        return $this->apiSuccess('your information has been updated successfull');
    }
    function delete(BillingInformation $billing)
    {
        $user = Auth::user();
        if ($billing->user_id != $user->id) {
            return $this->apiError('not your information');
        }
        $billing->delete();
        return $this->apiSuccess('your information has been deleted successfull');
    }
    function view()
    {
        $user = Auth::user();
        $address = BillingInformation::where('user_id', $user->id)->get();
        if (!$address) {
            return $this->apiError('address not found');
        }
        return $this->apiSuccess('User Billing Information', InformationResource::collection($address));
    }
}
