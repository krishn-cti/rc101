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
    public function loginToGoogle($role)
    {
        // Extract actual role (student or teacher)
        $actualRole = str_starts_with($role, 'teacher') ? 'teacher' : $role;

        if (!in_array($actualRole, ['student', 'teacher'])) {
            return response()->json(['success' => false, 'message' => 'Invalid role'], 400);
        }

        session_start();
        $_SESSION["google_login_role"] = $role;

        return Socialite::driver('google')
            ->stateless()
            ->scopes([
                'https://www.googleapis.com/auth/classroom.courses',
                'https://www.googleapis.com/auth/classroom.rosters',
                'https://www.googleapis.com/auth/classroom.coursework.students',
                'https://www.googleapis.com/auth/classroom.profile.emails',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email',
                'openid',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent select_account',
            ])
            ->redirect();
    }

    // public function handleGoogleCallback($google_login_role)
    // {
    //     // Determine actual role
    //     $actualRole = str_starts_with($google_login_role, 'teacher') ? 'teacher' : $google_login_role;

    //     // Determine redirected_from
    //     if ($actualRole == 'teacher') {
    //         $redirectedFrom = Str::after($google_login_role, 'teacher');
    //         $redirectedFrom = $redirectedFrom == '' ? 'dashboard' : $redirectedFrom;
    //     } else {
    //         $redirectedFrom = 'dashboard';
    //     }

    //     try {
    //         $googleUser = Socialite::driver('google')->stateless()->user();

    //         $user = User::where('google_id', $googleUser->id)->first();

    //         if ($user) {
    //             $user->update([
    //                 'google_token' => $googleUser->token,
    //                 'google_refresh_token' => $googleUser->refreshToken,
    //                 'redirected_from' => $redirectedFrom,
    //             ]);
    //         } else {
    //             $user = User::create([
    //                 'name' => $googleUser->name,
    //                 'email' => $googleUser->email,
    //                 'google_id' => $googleUser->id,
    //                 'google_token' => $googleUser->token,
    //                 'google_refresh_token' => $googleUser->refreshToken,
    //                 'google_profile_image' => $googleUser->avatar,
    //                 'role_id' => $actualRole === 'teacher' ? 3 : 2,
    //                 'google_classroom_role' => $actualRole,
    //                 'status' => 1,
    //                 'redirected_from' => $redirectedFrom,
    //                 'password' => bcrypt(Str::random(16)),
    //             ]);
    //         }

    //         $subscription = UserSubscription::where('user_id', $user->id)
    //             ->where('status', 1)
    //             ->with('subscription')
    //             ->orderByDesc('subscription_id')
    //             ->first();

    //         Auth::login($user);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Login successful',
    //             'user' => $user,
    //             'subscription' => $subscription // Return subscription details if available
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Google login failed',
    //             'details' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function handleGoogleCallback($google_login_role)
    {
        $actualRole = str_starts_with($google_login_role, 'teacher') ? 'teacher' : $google_login_role;

        if ($actualRole == 'teacher') {
            $redirectedFrom = Str::after($google_login_role, 'teacher');
            $redirectedFrom = $redirectedFrom == '' ? 'dashboard' : $redirectedFrom;
        } else {
            $redirectedFrom = 'dashboard';
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'redirected_from' => $redirectedFrom,
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'google_profile_image' => $googleUser->avatar,
                    'role_id' => $actualRole === 'teacher' ? 3 : 2,
                    'google_classroom_role' => $actualRole,
                    'status' => 1,
                    'redirected_from' => $redirectedFrom,
                    'password' => bcrypt(Str::random(16)),
                ]);
            }

            // Auto-expire old subscriptions
            UserSubscription::where('status', 1)
                ->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->update(['status' => 2]);

            // Fetch only valid active subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->where(function ($q) {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->with('subscription')
                ->orderByDesc('subscription_id')
                ->first();

            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'subscription' => $subscription
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
