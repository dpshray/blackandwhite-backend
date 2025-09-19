<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ResponseTrait;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

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
        $request->validate(['code' => 'required|string']);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->code); // <-- use code from frontend

            $email = $googleUser->getEmail();

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $googleUser->getName(),
                    'email_verified_at' => now(),
                    'is_admin' => 0,
                ]
            );

            if (!$user->profile) {
                $user->profile()->create(['user_id' => $user->id]);
            }

            $token = $user->createToken($email . '-Android')->plainTextToken;

            return $this->apiSuccess('Login successful', ['token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
            return $this->apiError('An error occurred.');
        }
    }
}
