<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class GoogleAuthMiddleware
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        $accessToken = $request->bearerToken();
        
        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Authorization token is required.'], 401);
        }

        // Find the user by the Google token
        $user = User::where('google_token', $accessToken)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid Google token.'], 401);
        }

        // Check role if specified
        if ($role && $user->google_classroom_role !== $role) {
            return response()->json([
                'success' => false,
                'message' => $role === 'teacher'
                    ? 'Access denied. Please log in with a teacher account.'
                    : 'Access denied. Please log in with a student account.'
            ], 403);
        }

        // Attach the authenticated user to the request
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
