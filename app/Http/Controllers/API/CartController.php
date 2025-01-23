<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Write code on Method for get cart details
     *
     * @return response()
     */
    public function getCartDetails(Request $request)
    {
        $cartItems = Cart::with(['productImages', 'productDetails'])
            ->where('user_id', $request->auth_user->id)
            ->latest()
            ->get();
        // dd($cartItems);

        // Check if the collection is empty
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty!',
            ], 200);
        }

        $response = [
            'success' => true,
            'message' => 'Cart Items retrieved successfully.',
            'data' => $cartItems,
        ];

        return response()->json($response, 200);
    }

    /**
     * Write code on Method for add to cart
     *
     * @return response()
     */
    public function addToCart(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $userId = $request->auth_user->id;

        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // If the product exists, update the quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
            $message = 'Cart updated successfully';
        } else {
            // If the product doesn't exist, create a new cart item
            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->product_id = $request->product_id;
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            $message = 'Product added to cart successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * Write code on Method for product update in cart
     *
     * @return response()
     */
    public function updateCart(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $cartItem = Cart::where('user_id', $request->auth_user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully'
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Product not found in cart',
            ], 200);
        }
    }

    /**
     * Write code on Method for product remove from cart
     *
     * @return response()
     */
    public function removeFromCart(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $cartItem = Cart::where('user_id', $request->auth_user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Delete the cart item
            $cartItem->delete();
            $message = 'Item removed from cart successfully';
        } else {
            // Cart item not found
            $message = 'Item not found in cart';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }
}
