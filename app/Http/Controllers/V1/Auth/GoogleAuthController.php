<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $request->validate(['token' => 'required|string']);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->token); // <-- use code from frontend

            $email = $googleUser->getEmail();
            $randomPassword = Str::random(12);
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $googleUser->getName(),
                    'email_verified_at' => now(),
                    'mobile_number' => '984000000',
                    'password' => Hash::make($randomPassword),
                    'is_admin' => 0,
                ]
            );

            if (!$user->profile) {
                $user->profile()->create(['user_id' => $user->id]);
            }

            $token = $user->createToken($user->email . '-AuthToken')->plainTextToken;
            $token = 'Bearer ' . $token;

            $user = new UserResource($user);
            return $this->apiSuccess('Welcome', [
                'data' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
            return $this->apiError('An error occurred.');
        }
    }
}
