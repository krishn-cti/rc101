<?php

namespace App\Http\Controllers;
       
use Illuminate\Http\Request;
use Stripe;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
       
class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe(): View
    {
        return view('stripe');
    }
      
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request): RedirectResponse
    {
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $productData = $stripe->products->create(['name' => 'Gold Plan', 'description' => 'test']);
        
            $priceData = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => 1000,
                'product' => $productData->id,
            ]);

            $sessionData = $stripe->checkout->sessions->create([
                'success_url' => env('FRONT_URL') . '/order-success',
                'cancel_url' => env('FRONT_URL') . '/order-cancel',
                'line_items' => [
                    [
                        'price' => $priceData->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
            ]);
            dd($sessionData);
            // Payment successful, redirect to thank you page
            return redirect(env('FRONT_URL') . '/order-success');
        } catch (Stripe\Exception\ApiErrorException $e) {
            // Payment failed, redirect to order-cancel page
            return redirect(env('FRONT_URL') . '/order-cancel');
        }        
    }
}