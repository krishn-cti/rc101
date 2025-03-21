<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Bot;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\Rule;
use Auth;
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
            'email' => 'required|string|email|max:150|unique:users,email',
            'number' => 'nullable|numeric|digits_between:10,15',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
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
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'role_id' => 4,
            'show_password' => $request->password,
            'password' => Hash::make($request->password)
        ])->sendEmailVerificationNotification();

        // $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'success' => true,
            'message' => 'Team account is created and verification link sent on your email id.',
            // 'data' => $data
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
        $user = User::where('email', $request->email)
            ->where('role_id', 4)
            ->with(['bot', 'weightClass'])
            ->first();

        // Check email verification and password
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 500);
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
            ], 500);
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'success' => true,
            'message' => 'Team Member is logged in successfully.',
            'data' => $data,
        ];

        // Set is_popup_display to 1 if it's the first login, otherwise 0
        if (is_null($user->login_count) || $user->login_count == 0) {
            $user->is_popup_display = 1;
        } else {
            $user->is_popup_display = 0;
        }

        // Increment login count
        $user->login_count++;
        $user->save();

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
    public function forgotPassword(Request $request)
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

    // this method is used to get all the team members
    public function getAllMembers()
    {
        $users = User::where('role_id', 4)
            ->with([
                'bot' => function ($query) {
                    $query->with(['botType', 'weightClass']);
                },
                'allBots' => function ($query) {
                    $query->with(['botType', 'weightClass']);
                },
                'weightClass'
            ])
            ->orderBy('name', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Team members fetched successfully',
            'data' => $users
        ], 200);
    }

    // this method is used to get all the team members
    public function updateMemberDetail(Request $request)
    {
        // Retrieve the user by ID
        $user = User::where('id', $request->user_id)
            ->where('role_id', 4)
            ->with(['bot', 'weightClass'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found or not authorized for this action.',
            ], 404);
        }

        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:150|unique:users,email,' . $user->id,
            'number' => 'nullable|numeric|digits_between:8,15',
            'designation' => 'nullable|string|max:255',
            'about' => 'nullable|string',
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
        $user->designation = $request->designation ?? $user->designation;
        $user->website_link = $request->website_link ?? $user->website_link;
        $user->discord_name = $request->discord_name ?? $user->discord_name;
        $user->bot_id = $request->bot_id ?? $user->bot_id;
        $user->weight_class_id = $request->weight_class_id ?? $user->weight_class_id;
        $user->tournament = $request->tournament ?? $user->tournament;
        $user->city = $request->city ?? $user->city;
        $user->state = $request->state ?? $user->state;
        $user->country = $request->country ?? $user->country;
        $user->about = $request->about ?? $user->about;

        // Update password if provided
        if ($request->password) {
            $user->show_password = $request->password;
            $user->password = Hash::make($request->password);
        }

        // Handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && file_exists(public_path('profile_images/' . $user->profile_image))) {
                unlink(public_path('profile_images/' . $user->profile_image));
            }

            // Upload the new profile image
            $fileName = uniqid() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move(public_path('profile_images'), $fileName);
            $user->profile_image = $fileName;
        }

        $user->save();

        // Return the response
        return response()->json([
            'success' => true,
            'message' => 'Team member details updated successfully.',
            'data' => $user,
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
            ], 400);
        }

        // Verify if the token matches the one stored in the database
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token provided!'
            ], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Delete the password reset record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

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
        $user = $request->auth_user;
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'User is logged out successfully'
        ], 200);
    }

    /**
     * update member profile from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = $request->auth_user;

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
     * Delete member from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteMember(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $member = User::where('id', $request->member_id)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.',
            ], 404);
        }

        // Delete all bots associated with the member (check if the relation exists)
        if ($member->allBots()->exists()) {
            $member->allBots()->delete();
        }

        // Delete the member
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member and all associated bots deleted successfully.',
        ], 200);
    }

    /**
     * Delete member from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteBot(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'bot_id' => 'required|exists:cms_bots,id',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }
        $bot = Bot::find($request->bot_id);

        if (!$bot) {
            return response()->json([
                'success' => false,
                'message' => 'Bot not found.'
            ], 404);
        }

        // Delete the bot
        $bot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bot deleted successfully.',
        ], 200);
    }

    /**
     * get logged in member details from application.
     * @return \Illuminate\Http\Response
     */
    public function getMemberDetail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // Fetch the member with related bots, bot type, and weight class
        // $member = User::where('role_id', 4)
        //     ->where('id', $request->member_id)
        //     ->with([
        //         'allBots' => function ($query) {
        //             $query->with(['botType', 'weightClass']);
        //         }
        //     ])
        //     ->first();

        $member = User::where('role_id', 4)
            ->where('id', $request->member_id)
            ->with([
                'bot',
                'weightClass',
                'allBots' => function ($query) {
                    $query->with(['botType', 'weightClass']);
                }
            ])
            ->orderBy('name', 'ASC')
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member details fetched successfully.',
            'data' => $member,
        ], 200);
    }

    /**
     * get logged in user details from application.
     * @return \Illuminate\Http\Response
     */
    public function getMyProfile(Request $request)
    {
        $user = $request->auth_user;
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
        $userId = $request->auth_user->id;
        // $user_id = $request->user()->id;
        $users = User::with('userAddresses')
            ->where('users.id', $userId)
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

        $userAddress = UserAddress::where('user_id', $request->auth_user->id)
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
