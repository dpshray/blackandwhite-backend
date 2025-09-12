<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //
    use ResponseTrait;
    public function view_profile()
    {
        $user = Auth::user();
        $user = User::with('profile')->find($user->id);

        // If profile does not exist, create one
        if (!$user->profile) {
            $profile = Profile::create([
                'user_id' => $user->id,
            ]);
            $user->setRelation('profile', $profile);
        }

        $profile = new ProfileResource($user);

        return $this->apiSuccess('user profile', $profile);
    }


    function edit_profile(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return $this->apiError('profile not found');
        }

        $profile->update($request->validated());
        if ($request->hasFile('image')) {
            //clear old images first
            $profile->clearMediaCollection(Profile::MEDIA_NAME);
            $profile->addMedia($request->image)->toMediaCollection(Profile::MEDIA_NAME);
        }
        return $this->apiSuccess('Profile updated successfully',);
    }
}
