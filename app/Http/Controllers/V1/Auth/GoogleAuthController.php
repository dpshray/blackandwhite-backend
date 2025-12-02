<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    //
    use ResponseTrait;
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback(Request $request)
    {
        try {

            // Get user from Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $email = $googleUser->getEmail();

            // Check if user already exists
            $user = User::where('email', $email)->first();

            // If not exist, create new user
            if (!$user) {
                $randomPassword = Str::random(12);

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'mobile_number' => '984000000',
                    'password' => Hash::make($randomPassword),
                    'is_admin' => 0,
                ]);

                // Create profile for new user
                $user->profile()->create([
                    'user_id' => $user->id
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken($user->email . '-AuthToken')->plainTextToken;
            $token = 'Bearer ' . $token;

            return $this->apiSuccess('Welcome', [
                'data' => new UserResource($user),
                'token' => $token
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->apiError('An error occurred.');
        }
    }
}
