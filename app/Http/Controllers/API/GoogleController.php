<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // public function loginToGoogle($role)
    // {
    //     if (!in_array($role, ['student', 'teacher'])) {
    //         return response()->json(['success' => false, 'message' => 'Invalid role'], 400);
    //     }

    //     session_start();
    //     $_SESSION["google_login_role"] = $role;

    //     // return Socialite::driver('google')->stateless()->redirect();
    //     return Socialite::driver('google')
    //         ->stateless()
    //         ->scopes([
    //             'https://www.googleapis.com/auth/classroom.courses',
    //             'https://www.googleapis.com/auth/classroom.rosters',
    //             'https://www.googleapis.com/auth/classroom.rosters.readonly',
    //             'https://www.googleapis.com/auth/classroom.coursework.students',
    //             'https://www.googleapis.com/auth/classroom.profile.emails'
    //         ])
    //         // ->with(['access_type' => 'offline', 'prompt' => 'consent'])
    //         ->redirect();
    // }

    public function loginToGoogle($role)
    {
        if (!in_array($role, ['student', 'teacher'])) {
            return response()->json(['success' => false, 'message' => 'Invalid role'], 400);
        }

        session_start();
        $_SESSION["google_login_role"] = $role;

        return Socialite::driver('google')
            ->stateless()
            ->scopes([
                'https://www.googleapis.com/auth/classroom.courses',
                'https://www.googleapis.com/auth/classroom.rosters',
                'https://www.googleapis.com/auth/classroom.rosters.readonly',
                'https://www.googleapis.com/auth/classroom.coursework.students',
                'https://www.googleapis.com/auth/classroom.profile.emails',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/classroom.coursework.me'
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function handleGoogleCallback($google_login_role)
    {
        try {
            if (!$google_login_role || !in_array($google_login_role, ['student', 'teacher'])) {
                return response()->json(['success' => false, 'message' => 'Role not defined or invalid'], 400);
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'google_profile_image' => $googleUser->avatar,
                    'role_id' => $google_login_role === 'teacher' ? 3 : 2, // 3 for Teacher, 2 for Student
                    'google_classroom_role' => $google_login_role,
                    'status' => 1,
                    'password' => bcrypt(Str::random(16)), // Temporary password
                ]);
            }

            // Fetch subscription details if available
            // $subscription = UserSubscription::where('user_id', $user->id)
            //     ->where('status', 1) // Assuming status 1 means active subscription
            //     ->with('subscription') // Assuming there is a relationship set up with the Subscription model
            //     ->first();

            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->with('subscription')
                ->orderByDesc('subscription_id')
                ->first();

            // Log the user in
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'subscription' => $subscription // Return subscription details if available
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google login failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
