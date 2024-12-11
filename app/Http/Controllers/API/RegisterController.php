<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class RegisterController extends BaseController
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|string|email:rfc,dns|max:150|unique:users,email',
            'number' => 'required|numeric|digits_between:10,15',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'show_password' => $request->password,
            'password' => Hash::make($request->password)
        ])->sendEmailVerificationNotification();

        // $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'success' => true,
            'message' => 'User is created and verification link sent on your email id.',
        ];

        return response()->json($response, 201);
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // Check email existence
        $user = User::where('email', $request->email)->first();

        // Check email verification and password
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Email is not verified. Please verify your email to log in.'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'success' => true,
            'message' => 'User is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword11(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $user = DB::table('users')->where('email', '=', $request->email)->first();

        //Check if the user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User does not exist"
            ], 400);
        }

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(64),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();

        if (Password::sendResetEmail($request->email, $tokenData->token)) {
            return response()->json([
                'success' => true,
                'message' => "Please check your email for a password reset link.",
                'token' => $tokenData->token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "A Network Error occurred. Please try again."
            ], 400);
        }
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword12(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $verify = User::where('email', $request->email)->exists();

        if ($verify) {
            $userData = DB::table('password_resets')->where('email', $request->email)->first();

            if (!$userData) {
                $token = Str::random(64);
                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            } else {
                $token = $userData->token;
            }

            Password::sendResetLink($request->only('email'));

            return response()->json([
                'success' => true,
                'message' => "Please check your email for a password reset link."
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "This email does not exist"
            ], 400);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $verify = User::where('email', $request->email)->exists();

        if ($verify) {
            $userData = DB::table('password_resets')->where('email', $request->email)->first();
            // dd($userData);
            // $token = Str::random(64);
            // $password_reset = DB::table('password_resets')->insert([
            //     'email' => $request->email,
            //     'token' =>  $token,
            //     'created_at' => Carbon::now()
            // ]);

            // if ($password_reset) {
            // Mail::send('auth.forgot_password', ['token' => $token], function($message) use($request){
            //     $message->to($request->email);
            //     $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
            //     $message->subject('Reset Password');
            // });

            // Password::sendResetLink($request->all());
            Password::sendResetLink(
                $request->only('email')
            );
            return response()->json([
                'success' => true,
                'message' => "Please check your email for a password reset link."
            ], 200);
            // }
        } else {
            return response()->json([
                'success' => false,
                'message' => "This email does not exist"
            ], 400);
        }
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function resetPassword(Request $request)
    // {
    //     $validate = Validator::make($request->all(), [
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'new_password' => 'required|string|min:8',
    //         'confirm_password' => 'required|same:new_password'
    //     ]);

    //     if ($validate->fails()) {
    //         return back()->withErrors($validate)->withInput(); // Return errors to Blade
    //     }

    //     $update = DB::table('password_resets')->where(['email' => $request->email, 'token' => $request->token])->first();

    //     if (!$update) {
    //         return back()->withErrors(['email' => 'Invalid token provided!'])->withInput(); // Handle error for invalid token
    //     }

    //     $user = User::where('email', $request->email)->update([
    //         'password' => Hash::make($request->new_password)
    //     ]);

    //     // Delete password_resets record
    //     DB::table('password_resets')->where(['email' => $request->email])->delete();

    //     return redirect()->route('login')->with('status', 'Your password has been successfully changed!');
    // }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $update = DB::table('password_resets')->where(['email' => $request->email, 'token' => $request->token])->first();

        if (!$update) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token provided!'
            ], 400);
        }

        $user = User::where('email', $request->email)->update([
            'show_password' => $request->password,
            'password' => Hash::make($request->new_password)
        ]);

        // Delete password_resets record
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your password has been successfully changed!'
        ], 200);
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'User is logged out successfully'
        ], 200);
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:150|unique:users,email,' . $user->id,
            'number' => 'nullable|numeric|digits_between:8,15',
            'password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|same:password',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number ?? $user->number;

        if ($request->password) {
            $user->show_password = $request->password;
            $user->password = Hash::make($request->password);
        }

        // Check if profile image is provided
        if ($request->hasFile('profile_image')) {
            // Delete the previous profile image if it exists
            if ($user->profile_image && file_exists(public_path('profile_images/' . $user->profile_image))) {
                unlink(public_path('profile_images/' . $user->profile_image));
            }

            // Upload the new profile image
            $fileName = uniqid() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move(public_path('profile_images'), $fileName);
            $user->profile_image = $fileName;
        }

        $user->save();

        // Prepare and return the response
        return response()->json([
            'success' => true,
            'message' => 'User details updated successfully.',
            'data' => $user,
        ], 200);
    }


    /**
     * get logged in user details from application.
     * @return \Illuminate\Http\Response
     */
    public function getMyProfile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'message' => 'User details fetched successfully',
            'data' => $user
        ], 200);
    }

    /**
     * get logged in user addresses from application.
     * @return \Illuminate\Http\Response
     */
    public function getMyAddresses(Request $request)
    {
        $user_id = $request->user()->id;
        $users = User::with('userAddresses')
            ->where('users.id', $user_id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'User addresses fetched successfully',
            'data' => $users
        ], 200);
    }

    /**
     * delete logged in user's address from application.
     * @return \Illuminate\Http\Response
     */
    public function removeBillingAddress(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $userAddress = UserAddress::where('user_id', $request->user()->id)
            ->where('id', $request->address_id)
            ->first();

        if ($userAddress) {
            $userAddress->delete();
            $message = 'User billing address removed successfully';
        } else {
            $message = 'User address not found';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }
}
