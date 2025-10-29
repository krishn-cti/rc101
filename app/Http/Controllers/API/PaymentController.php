<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;

class PaymentController extends Controller
{
    // API to get subscription plans without login
    public function getAllSubscription(Request $request)
    {
        $subscriptions = Subscription::all();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subscriptions available.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions
        ]);
    }

    public function getSubscriptions(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
        }

        $teacher = User::where('google_token', $accessToken)->first();

        if (!$teacher || $teacher->google_classroom_role !== "teacher") {
            return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
        }

        // Fetch all available subscriptions
        $subscriptions = Subscription::all();

        // Fetch the user's active subscription (if any)
        // $userSubscription = UserSubscription::where('user_id', $teacher->id)
        //     ->where('status', 1) // Only fetch active subscriptions
        //     ->with('subscription') // Eager load subscription details
        //     ->first();
        $userSubscription = UserSubscription::where('user_id', $teacher->id)
            ->where('status', 1)
            ->with('subscription')
            ->orderByDesc('subscription_id')
            ->first();

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions,
            'user_subscription' => $userSubscription
        ]);
    }


    // API to initiate checkout session
    // public function checkout(Request $request)
    // {
    //     $accessToken = $request->bearerToken();

    //     if (!$accessToken) {
    //         return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
    //     }

    //     $teacher = User::where('google_token', $accessToken)->first();

    //     if (!$teacher || $teacher->google_classroom_role !== "teacher") {
    //         return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
    //     }

    //     // Check if the user already has an active subscription
    //     $existingSubscription = UserSubscription::where('user_id', $teacher->id)
    //         ->where('subscription_id', $request->subscription_id)
    //         ->where('status', 1)
    //         ->first();

    //     if ($existingSubscription) {
    //         return response()->json(['success' => false, 'message' => 'User already has an active subscription'], 400);
    //     }

    //     $request->validate([
    //         'subscription_id' => 'required|exists:subscriptions,id',
    //         'type' => 'required|in:monthly,yearly',
    //     ]);

    //     $subscription = Subscription::findOrFail($request->subscription_id);
    //     $price = $request->type === 'monthly' ? $subscription->monthly_price : $subscription->yearly_price;

    //     if (!$price || $price <= 0) {
    //         return response()->json(['success' => false, 'message' => 'Invalid price'], 400);
    //     }

    //     try {
    //         Stripe::setApiKey(env('STRIPE_SECRET'));

    //         $session = Session::create([
    //             'payment_method_types' => ['card'],
    //             'customer_email' => $teacher->email,
    //             'line_items' => [[
    //                 'price_data' => [
    //                     'currency' => 'usd',
    //                     'product_data' => [
    //                         'name' => $subscription->name . ' (' . ucfirst($request->type) . ')',
    //                     ],
    //                     'unit_amount' => $price * 100,
    //                     'recurring' => ['interval' => $request->type === 'monthly' ? 'month' : 'year'],
    //                 ],
    //                 'quantity' => 1,
    //             ]],
    //             'mode' => 'subscription',
    //             'success_url' => env('FRONT_URL') . '/teacher/payment-success?session_id={CHECKOUT_SESSION_ID}',
    //             'cancel_url' => env('FRONT_URL') . '/teacher/payment-failed',
    //             'metadata' => [
    //                 'subscription_name' => $subscription->name,
    //                 'type' => $request->type,
    //             ],
    //         ]);

    //         return response()->json(['success' => true, 'session_url' => $session->url]);
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 500);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
    //     }
    // }

    public function checkout(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
        }

        $teacher = User::where('google_token', $accessToken)->first();

        if (!$teacher || $teacher->google_classroom_role !== "teacher") {
            return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
        }

        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'type' => 'required|in:monthly,yearly,free',
        ]);

        // Prevent multiple active subscriptions
        $existingSubscription = UserSubscription::where('user_id', $teacher->id)
            ->where('status', 1)
            ->first();

        if ($existingSubscription) {
            return response()->json(['success' => false, 'message' => 'User already has an active subscription'], 400);
        }

        $subscription = Subscription::findOrFail($request->subscription_id);

        // Handle Free Plan (No Stripe checkout)
        if ($request->type === 'free') {
            // Check if user already used free trial before
            $usedFreeTrial = UserSubscription::where('user_id', $teacher->id)
                ->where('type', 'free')
                ->exists();

            if ($usedFreeTrial) {
                return response()->json(['success' => false, 'message' => 'Free trial already used. Please choose a paid plan.'], 400);
            }

            // Create 3-day free trial
            UserSubscription::create([
                'user_id' => $teacher->id,
                'subscription_id' => $subscription->id,
                'type' => 'free',
                'start_date' => now(),
                'end_date' => now()->addDays(3),
                'stripe_subscription_id' => null,
                'status' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Free 3-day trial activated successfully',
                'data' => [
                    'start_date' => now()->toDateTimeString(),
                    'end_date' => now()->addDays(3)->toDateTimeString(),
                ],
            ]);
        }

        // For Paid Plans (Monthly/Yearly)
        $price = $request->type === 'monthly' ? $subscription->monthly_price : $subscription->yearly_price;

        if (!$price || $price <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid price'], 400);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $teacher->email,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $subscription->name . ' (' . ucfirst($request->type) . ')',
                        ],
                        'unit_amount' => $price * 100,
                        'recurring' => ['interval' => $request->type === 'monthly' ? 'month' : 'year'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => env('FRONT_URL') . '/teacher/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('FRONT_URL') . '/teacher/payment-failed',
                'metadata' => [
                    'subscription_name' => $subscription->name,
                    'type' => $request->type,
                ],
            ]);

            return response()->json(['success' => true, 'session_url' => $session->url]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    // API to handle payment success
    public function success(Request $request)
    {
        $request->validate([
            'session_id' => 'required'
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);

            if (!$session) {
                return response()->json(['success' => false, 'message' => 'Invalid session ID'], 400);
            }

            // Fetch user using email from Stripe session
            $user = User::where('email', $session->customer_email)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Validate metadata exists
            if (!isset($session->metadata['subscription_name'], $session->metadata['type'])) {
                return response()->json(['success' => false, 'message' => 'Subscription details missing'], 400);
            }

            $subscription = Subscription::where('name', $session->metadata['subscription_name'])->first();

            if (!$subscription) {
                return response()->json(['success' => false, 'message' => 'Subscription not found'], 404);
            }

            // Check if the user already has an active subscription
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $subscription->id)
                ->where('status', 1)
                ->first();

            if ($existingSubscription) {
                return response()->json(['success' => false, 'message' => 'User already has an active subscription'], 400);
            }

            // Save the subscription
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'type' => $session->metadata['type'],
                'start_date' => now(),
                'end_date' => $session->metadata['type'] === 'monthly' ? now()->addMonth() : now()->addYear(),
                'stripe_subscription_id' => $session->subscription, // Fetch correct Stripe subscription ID
                'status' => 1,
            ]);

            return response()->json(['success' => true, 'message' => 'Subscription activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    // API to cancel subscription
    public function cancelSubscription(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
        }

        $teacher = User::where('google_token', $accessToken)->first();

        if (!$teacher || $teacher->google_classroom_role !== "teacher") {
            return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
        }

        $request->validate([
            'subscription_id' => 'required|exists:user_subscriptions,id'
        ]);

        // Fetch the user subscription safely
        $userSubscription = UserSubscription::where('subscription_id', $request->subscription_id)
            ->where('user_id', $teacher->id)
            ->where('status', 1)
            ->first();

        if (!$userSubscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found or does not belong to this user'], 404);
        }

        if ($userSubscription->status == 0) {
            return response()->json(['success' => false, 'message' => 'Subscription is already canceled'], 400);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!$userSubscription->stripe_subscription_id) {
                return response()->json(['success' => false, 'message' => 'Invalid Stripe subscription ID'], 400);
            }

            // Retrieve subscription
            $subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_subscription_id);

            if (!$subscription || $subscription->status === 'canceled') {
                return response()->json(['success' => false, 'message' => 'Subscription already canceled or not found in Stripe'], 400);
            }

            // Cancel the subscription
            $subscription->cancel();

            // Update subscription status in the database
            $userSubscription->update(['status' => 0]);

            return response()->json(['success' => true, 'message' => 'Subscription canceled successfully']);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
