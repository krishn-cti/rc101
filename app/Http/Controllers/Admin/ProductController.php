<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with(['category', 'subCategory', 'productImages', 'productReviews'])
                ->where('status', 1)
                ->latest()
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->editColumn('category', function ($row) {
                    if (!empty($row->category_id)) {
                        return $row['category']->category_name;
                    } else {
                        return '#N/A';
                    }
                })
                ->editColumn('sub_category', function ($row) {
                    if (!empty($row->sub_category_id)) {
                        return $row['subCategory']->sub_category_name;
                    } else {
                        return '#N/A';
                    }
                })
                ->addColumn('thumbnail', function ($row) {
                    if (!empty($row['productImages']->thumbnail)) {
                        $src = $row['productImages']->thumbnail;
                        $image = '<img src="' . $src . '" alt="Profile Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_product_img.png') . '" alt="Profile Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('edit-product/' . $row->id) . '">
                                        <lord-icon data-bs-toggle="modal" data-bs-target="#ct_edit_product" src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" colors="primary:#333333,secondary:#333333" style="width:20px;height:20px">
                                        </lord-icon>
                                    </a>
                        <a href="javascript:;"  title="Delete" onclick="deleteConfirm(' . $row->id . ')"><lord-icon src="https://cdn.lordicon.com/drxwpfop.json"
                        trigger="hover"
                        colors="primary:#ff0000,secondary:#ff0000"
                        style="width:20px;height:20px">
                    </lord-icon></a>
                     </div>';
                    return $btn;
                })
                ->rawColumns(['profile_image', 'action', 'thumbnail'])
                ->make(true);
        }

        // return view('admin.user.list');
        return view('admin/product/list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::get();
        $sub_category = SubCategory::get();
        return view('admin.product.add', compact('category', 'sub_category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:100',
            'related_name' => 'required|string|max:100',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string|max:255',
            'thumbnail' => 'required|image|max:2048', // 2MB
            'images.*' => 'image|max:2048', // 2MB per image
            'images' => 'max:8', // Maximum 8 images
        ], [], [
            'category_id' => 'category',
            'sub_category_id' => 'sub category',
        ]);

        // Update the user's profile information
        // $user = Auth::user();
        $product = new Product();
        $product->sku = Str::random(16);
        $product->product_name = $request->product_name;
        $product->related_name = $request->related_name;
        $product->description = $request->description;
        $product->long_description = $request->long_description;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->save();

        if ($request->hasFile('thumbnail') || $request->hasFile('images')) {
            $productImage = new ProductImage();
        }
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $fileName = uniqid() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move(public_path('products'), $fileName);

            $productImage->product_id = $product->id;
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
                ['product_id' => $product->id],
                ['images' => $imagesString]
            );
        }

        Session::flash('message', 'Product Added Succesfully !');
        Session::flash('alert-class', 'success');
        return redirect('list-product');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['category'] = Category::get();
        $data['sub_category'] = SubCategory::get();
        $data['product'] = Product::where('id', $id)->with('productImages')->first();
        if (!empty($data['product']->productImages->images)) {
            $data['other_images'] = $data['product']->productImages->images;
        } else {
            $data['other_images'] = '';
        }
        return view('admin.product.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // dd($request->all());
        $request->validate([
            'product_name' => 'required|string|max:100',
            'related_name' => 'required|string|max:100',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|max:2048', // Optional during update
            'images.*' => 'image|max:2048', // 2MB per image
            'images' => 'max:8', // Maximum 8 images
        ], [], [
            'category_id' => 'category',
            'sub_category_id' => 'sub category',
        ]);

        // Update the user's profile information
        // $user = Auth::user();
        $product = Product::where('id', $request->product_id)->first();
        $product->product_name = $request->product_name;
        $product->related_name = $request->related_name;
        $product->description = $request->description;
        $product->long_description = $request->long_description;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->save();

        if ($request->hasFile('thumbnail') || $request->hasFile('images')) {
            $productImage = ProductImage::where('product_id', $request->product_id)->first();
        }
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $fileName = uniqid() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->move(public_path('products'), $fileName);

            $productImage->product_id = $product->id;
            $productImage->thumbnail = $fileName;
            $productImage->save();
        }

        // Save additional images
        if ($request->hasFile('images')) {
            $deleteOtherImage = ProductImage::where('product_id', $request->product_id)->update(['images' => NULL]);
            $imageUrls = [];
            foreach ($request->file('images') as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('products'), $fileName);
                $imageUrls[] = $fileName;
            }
            $imagesString = implode(',', $imageUrls);

            $productImage = ProductImage::updateOrCreate(
                ['product_id' => $product->id],
                ['images' => $imagesString]
            );
        }

        Session::flash('message', 'Product Updated Succesfully !');
        Session::flash('alert-class', 'success');
        return redirect('list-product');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $productImage = ProductImage::where('product_id', $request->id)->first();

        if (!empty($productImage)) {
            $productImage->delete();
            $product = Product::where('id', $request->id)->first();
            if (!empty($product)) {
                $productImage->delete();
                $product->delete();
                return true;
            }
            return "Something went wrong";
        } else {
            $product = Product::where('id', $request->id)->first();
            $product->delete();
            return true;
        }
    }

    public function getSubCategory(Request $request)
    {
        $subCategories = SubCategory::where('category_id', $request->category_id)->pluck('sub_category_name', 'id');
        return response()->json($subCategories);
    }
}
