<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ProductReview;
use App\Models\ProductImage;
use App\Models\Product;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['category', 'subCategory', 'productImages', 'productReviews'])
            ->latest()
            ->get();

        // Check if the collection is empty
        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No product found!',
            ], 200);
        }

        $response = [
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_name' => 'required|string|max:250|unique:products,product_name',
            'related_name' => 'required|string|max:250',
            'description' => 'required|string|',
            'price' => 'required|between:0,99.99',
            'discount' => 'required|between:0,99.99',
            'quantity' => 'required|numeric|',
            'thumbnail' => 'required|image',
            'images.*' => 'image'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // $product = Product::create($request->all());
        $product = new Product();
        $product->sku = Str::random(16);
        $product->product_name = $request->product_name;
        $product->related_name = $request->related_name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->save();

        $productId = $product->id;
        // Save the main product image
        if ($request->hasFile('thumbnail') || $request->hasFile('images')) {
            $productImage = new ProductImage();
        }
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $fileName = uniqid() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move(public_path('products'), $fileName);

            $productImage->product_id = $productId;
            $productImage->thumbnail = $fileName;
            $productImage->save();
        }

        // Save additional images
        if ($request->hasFile('images')) {
            $imageUrls = [];
            foreach ($request->file('images') as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('products'), $fileName);
                $imageUrls[] = $fileName;
            }
            $imagesString = implode(',', $imageUrls);

            $productImage = ProductImage::updateOrCreate(
                ['product_id' => $productId],
                ['images' => $imagesString]
            );
        }

        $response = [
            'success' => true,
            'message' => 'Product added successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'subCategory', 'productImages', 'productReviews'])->find($id);

        if (is_null($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not found!',
            ], 200);
        }

        $response = [
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 200);
        }

        $product->update($request->all());

        $response = [
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 200);
        }

        Product::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ], 200);
    }

    /**
     * Search by a related product name
     *
     * @param  str  $related_name
     * @return \Illuminate\Http\Response
     */
    public function getRelatedProduct($related_name)
    {
        $products = Product::with(['category', 'subCategory', 'productImages', 'productReviews'])->where('related_name', 'like', '%' . $related_name . '%')
            ->latest()->get();

        // dd($products);
        if (is_null($products->first())) {
            return response()->json([
                'success' => false,
                'message' => 'No product found!',
            ], 200);
        }

        $response = [
            'success' => true,
            'message' => 'Related products retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
    }

    /**
     * Send reviews for product
     *
     * @param  str  $product_id
     * @return \Illuminate\Http\Response
     */
    public function sendProductReview(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required',
            'rating' => 'required|numeric|between:1,5',
            'review_text' => 'required|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $productReviewData = ProductReview::where('product_id', $request->product_id)
            ->where('user_id', $request->auth_user->id)
            ->first();

        if ($productReviewData) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a rating for this product.'
            ], 400);
        } else {
            $productReview = new ProductReview();
            $productReview->product_id = $request->product_id;
            $productReview->user_id = $request->auth_user->id;
            $productReview->rating = $request->rating;
            $productReview->review_text = $request->review_text;
            $isSavedRating = $productReview->save();

            if ($isSavedRating) {
                $totalReviewCount = ProductReview::where('product_id', $request->product_id)->count();
                $totalRating = ProductReview::where('product_id', $request->product_id)->sum('rating');

                $averageRating = $totalReviewCount > 0 ? round($totalRating / $totalReviewCount, 1) : 0;

                // Update the product's total_rating
                Product::where('id', $request->product_id)->update(['total_rating' => $averageRating]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully.'
            ], 200);
        }
    }
}
