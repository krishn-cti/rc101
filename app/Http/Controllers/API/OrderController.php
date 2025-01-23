<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserOrder;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Product;
use App\Mail\SendOrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Stripe;

class OrderController extends Controller
{
    /**
     * Write code on Method for get my orders
     *
     * @return response()
     */
    public function getMyOrders(Request $request)
    {
        $orderItems = Order::with(['productImages', 'productDetails'])
            ->where('user_id', $request->auth_user->id)
            ->latest()
            ->get();

        // Check if the collection is empty
        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You have no orders yet!',
            ], 201);
        }

        $response = [
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => $orderItems,
        ];

        return response()->json($response, 200);
    }

    /**
     * Write code on Method for placed order
     *
     * @return response()
     */
    public function placeOrder(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'number' => 'required',
            'country' => 'required',
            'state' => 'required',
            'postal_code' => 'required',
            'address_line_1' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $userId = $request->auth_user->id;
        $grandTotal = $request->total_order_price;

        $cartItems = Cart::with('productDetails')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found in the cart',
            ], 201);
        }

        $transactionId = Str::random(64);

        foreach ($cartItems as $cartItem) {
            $orderItem = new Order();
            $orderItem->transaction_id = $transactionId;

            $discountedPrice = $cartItem['productDetails']->price - $cartItem['productDetails']->discount;
            $totalPrice = $discountedPrice * $cartItem->quantity;

            $orderItem->user_id = $cartItem->user_id;
            $orderItem->product_id = $cartItem->product_id;
            $orderItem->total_price = $totalPrice;
            $orderItem->payment_method = $request->payment_method;
            $orderItem->quantity = $cartItem->quantity;
            $orderItem->status = 1;

            $orderItem->save();
            $cartItem->delete();
        }

        $userData = User::find($userId);

        if ($userData) {
            $userAddress = UserAddress::where('user_id', $userData->id)->first();

            // Check if $userAddress exists and add_new_adddress is not set or set to 0
            if ($userAddress && !$request->has('add_new_adddress') || $request->add_new_adddress == 0) {
                $userData->default_address_id = $request->default_address_id;
                $userData->save();
            } else {
                $billingAddress = new UserAddress();
                $billingAddress->name = $request->name;
                $billingAddress->user_id = $userData->id;
                $billingAddress->email = $request->email;
                $billingAddress->number = $request->number;
                $billingAddress->company_name = $request->company_name;
                $billingAddress->address_line_1 = $request->address_line_1;
                $billingAddress->address_line_2 = $request->address_line_2;
                $billingAddress->postal_code = $request->postal_code;
                $billingAddress->other_notes = $request->other_notes;
                $billingAddress->state = $request->state;
                $billingAddress->country = $request->country;
                $billingAddress->save();

                $userData->default_address_id = $billingAddress->id;
                $userData->save();
            }
        }

        $stripeRes = $this->stripePost($grandTotal);

        if ($stripeRes) {
            $userOrder = new UserOrder();
            $userOrder->transaction_id = $transactionId;
            $userOrder->user_id = $userId;
            $userOrder->total_amount = $grandTotal;
            $userOrder->payment_mode = $request->payment_method;
            $userOrder->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'transaction_id' => $orderItem,
            'transaction_unique_id' => $orderItem->transaction_id,
            'payment_link' => $stripeRes
        ], 200);
    }

    /**
     * Write code on Method for create order
     *
     * @return response()
     */
    public function createOrder(Request $request)
    {
        $userId = $request->auth_user->id;
        $cartItems = Cart::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found in the cart',
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $cartItems
            ], 200);
        }
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost($total_amount)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $productData = $stripe->products->create(['name' => 'Gold Plan', 'description' => 'test']);

        $priceData = $stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $total_amount * 100,
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
        return $sessionData->url;
    }

    /**
     * Write code on Method for change order sataus
     *
     * @return response()
     */
    public function completeOrder(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'status' => 'required',
            'transaction_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }
        $userId = $request->auth_user->id;
        $transactionId = $request->transaction_id;

        if ($userId && $transactionId) {
            $userOrderItems = UserOrder::where('transaction_id', $transactionId)->first();
            $orderItems = Order::where('transaction_id', $transactionId)->get();

            if ($userOrderItems) {
                // Update payment status for UserOrder
                $userOrderItems->payment_status = $request->status;
                $userOrderItems->save();

                // Update payment status for each Order
                foreach ($orderItems as $orderItem) {
                    $orderItem->status = $request->status;
                    $orderItem->save();
                }


                foreach ($orderItems as $transaction) {
                    $product = Product::find($transaction->product_id);
                    $transaction->product_name = $product ? $product->product_name : null;
                    unset($transaction->product_id);
                }

                $order['billingAddress'] = UserAddress::where('id', $request->auth_user->default_address_id)->first();
                $order['userOrderItems'] = $userOrderItems;
                $order['orderItems'] = $orderItems;
                $order['orderStatus'] = $request->status;

                $emails = [$request->auth_user->email, $order['billingAddress']->email];
                $validEmails = array_filter($emails, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                Mail::to($validEmails)->send(new SendOrderConfirmationMail($order));

                return response()->json([
                    'success' => true,
                    'message' => 'Payment status updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No order found with this transaction ID',
                ], 404);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User ID or transaction ID not provided',
            ], 400);
        }
    }
}
