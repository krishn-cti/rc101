<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Subscription as StripeSubscription;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;

class PaymentController extends Controller
{
    // API to get subscription plans
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
        $userSubscription = UserSubscription::where('user_id', $teacher->id)
            ->where('status', 1) // Only fetch active subscriptions
            ->with('subscription') // Eager load subscription details
            ->first();

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions,
            'user_subscription' => $userSubscription
        ]);
    }


    // API to initiate checkout session
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

        // Check if the user already has an active subscription
        $existingSubscription = UserSubscription::where('user_id', $teacher->id)
            ->where('subscription_id', $request->subscription_id)
            ->where('status', 1)
            ->first();

        if ($existingSubscription) {
            return response()->json(['success' => false, 'message' => 'User already has an active subscription'], 400);
        }

        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'type' => 'required|in:monthly,yearly',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);
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

    public function checkout0(Request $request)
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
            'type' => 'required|in:monthly,yearly',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);
        $priceId = $request->type === 'monthly' ? $subscription->monthly_price : $subscription->yearly_price;
        
        // dd($priceId);
        // if (!$priceId) {
        //     return response()->json(['success' => false, 'message' => 'Invalid price ID'], 400);
        // }

        // Ensure Stripe API Key is set
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Check if the user already has an active subscription
        $existingSubscription = UserSubscription::where('user_id', $teacher->id)
            ->where('status', 1)
            ->first();

        if ($existingSubscription) {
            try {
                $stripeSubscription = StripeSubscription::retrieve($existingSubscription->stripe_subscription_id);
                // dd($stripeSubscription);
                $updatedSubscription = StripeSubscription::update($stripeSubscription->id, [
                    'items' => [[
                        'id' => $stripeSubscription->items->data[0]->id,
                        'price' => $priceId, // Use Stripe Price ID
                    ]],
                ]);

                $existingSubscription->update([
                    'subscription_id' => $subscription->id,
                    'type' => $request->type,
                    'start_date' => now(),
                    'end_date' => now()->add($request->type === 'monthly' ? 'month' : 'year'),
                ]);

                return response()->json(['success' => true, 'message' => 'Subscription upgraded successfully']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 500);
            }
        }

        // Create a new Checkout Session
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $teacher->email,
                'line_items' => [[
                    'price' => $priceId, // Use the predefined Stripe price ID
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => env('FRONT_URL') . '/teacher/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('FRONT_URL') . '/teacher/payment-failed',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'type' => $request->type,
                ],
            ]);

            return response()->json(['success' => true, 'session_url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 500);
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

    public function success0(Request $request)
    {
        $request->validate([
            'session_id' => 'required'
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::retrieve($request->session_id);

            if (!$session) {
                return response()->json(['success' => false, 'message' => 'Invalid session ID'], 400);
            }

            $user = User::where('email', $session->customer_email)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            if (!isset($session->metadata['subscription_id'], $session->metadata['type'])) {
                return response()->json(['success' => false, 'message' => 'Subscription details missing'], 400);
            }

            $newSubscription = Subscription::find($session->metadata['subscription_id']);

            if (!$newSubscription) {
                return response()->json(['success' => false, 'message' => 'Subscription not found'], 404);
            }

            $newPrice = $session->metadata['type'] === 'monthly' ? $newSubscription->monthly_price : $newSubscription->yearly_price;

            // Check if the user already has an active subscription
            $existingSubscription = UserSubscription::where('user_id', $user->id)->where('status', 1)->first();

            if ($existingSubscription) {
                $stripeSubscription = Subscription::retrieve($existingSubscription->stripe_subscription_id);

                $stripeSubscription->items = [[
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $session->metadata['subscription_id']
                ]];
                $stripeSubscription->save();

                $existingSubscription->update([
                    'subscription_id' => $newSubscription->id,
                    'type' => $session->metadata['type'],
                    'start_date' => now(),
                    'end_date' => now()->add($session->metadata['type'] === 'monthly' ? 'month' : 'year'),
                ]);

                return response()->json(['success' => true, 'message' => 'Subscription upgraded successfully']);
            }

            // Create a new subscription in the database
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $newSubscription->id,
                'type' => $session->metadata['type'],
                'start_date' => now(),
                'end_date' => now()->add($session->metadata['type'] === 'monthly' ? 'month' : 'year'),
                'stripe_subscription_id' => $session->subscription,
                'status' => 1,
            ]);

            return response()->json(['success' => true, 'message' => 'Subscription activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    // API to cancel subscription
    // public function cancelSubscription(Request $request)
    // {
    //     $accessToken = $request->bearerToken();

    //     if (!$accessToken) {
    //         return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
    //     }

    //     $teacher = User::where('google_token', $accessToken)->first();

    //     if (!$teacher || $teacher->google_classroom_role !== "teacher") {
    //         return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
    //     }

    //     $request->validate([
    //         'subscription_id' => 'required|exists:user_subscriptions,id'
    //     ]);

    //     // Fetch the user subscription safely
    //     $userSubscription = UserSubscription::where('id', $request->subscription_id)
    //         ->where('user_id', $teacher->id)
    //         ->first();

    //     if (!$userSubscription) {
    //         return response()->json(['success' => false, 'message' => 'Subscription not found or does not belong to this user'], 404);
    //     }

    //     try {
    //         Stripe::setApiKey(env('STRIPE_SECRET'));

    //         // Check if the Stripe subscription exists before retrieving
    //         if (!$userSubscription->stripe_subscription_id) {
    //             return response()->json(['success' => false, 'message' => 'Invalid Stripe subscription ID'], 400);
    //         }

    //         // Cancel the Stripe subscription
    //         $subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_subscription_id);
    //         $subscription->cancel();

    //         // Update subscription status in the database
    //         $userSubscription->update(['status' => 0]);

    //         return response()->json(['success' => true, 'message' => 'Subscription canceled successfully']);
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()], 500);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
    //     }
    // }
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
