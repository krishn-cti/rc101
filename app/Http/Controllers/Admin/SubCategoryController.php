<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SubCategory::where('status', 1)
                ->latest()
                ->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('serial_number', function($row) {
                        static $index = 0;
                        return ++$index;
                    })
                    ->editColumn('category_name', function($row){
                        $categoryData = Category::where('id', $row->category_id)->first();
                        return $categoryData->category_name;
                    })
                    ->addColumn('action', function($row) {
                        $btn = '<div class="d-flex align-items-center gap-3">
                                    <a href="' . url('edit-sub-category/' . $row->id) . '">
                                        <lord-icon data-bs-toggle="modal" data-bs-target="#ct_edit_product" src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" colors="primary:#333333,secondary:#333333" style="width:20px;height:20px">
                                        </lord-icon>
                                    </a>
                                    <a href="javascript:;" title="Delete" onclick="deleteConfirm(' . $row->id . ')">
                                        <lord-icon src="https://cdn.lordicon.com/drxwpfop.json" trigger="hover" colors="primary:#ff0000,secondary:#ff0000" style="width:20px;height:20px">
                                        </lord-icon>
                                    </a>
                                </div>';
                        return $btn;
                    })                    
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('admin/sub_category/list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoryData = Category::where('status',1)->get();
        return view('admin.sub_category.add',compact('categoryData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_category_name' => 'required|string|max:100',
            'category_id' => 'required',
            'description' => 'required|string|max:255',
        ], [], [
            'category_id' => 'category',
        ]);

        $category = new SubCategory();
        $category->sub_category_name = $request->sub_category_name;
        $category->category_id = $request->category_id;
        $category->description = $request->description;
        $category->save();
        
        if ($category) {
            return redirect('list-sub-category')->with( 'success_msg' , 'Sub Category added successfully!!' );
        } else {
            return redirect('list-sub-category')->with( 'error_msg' , 'Something went wrong!!' );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['categoryData'] = Category::where('status',1)->get();
        $data['sub_category'] = SubCategory::where('id', $id)->where('status', 1)->first();
        return view('admin.sub_category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'sub_category_name' => 'required|string|max:100',
            'category_id' => 'required',
            'description' => 'required|string|max:255'
        ], [], [
            'category_id' => 'category',
        ]);

        $id = $request->id;
        $sub_category = SubCategory::find($id);
        if (!$sub_category) {
            return redirect('list-sub-category')->with('error_msg', 'Sub Category not found.');
        }
        
        $sub_category->category_id = $request->category_id;
        $sub_category->sub_category_name = $request->sub_category_name;
        $sub_category->description = $request->description;
        $sub_category->save();
    
        if ($sub_category) {
            return redirect('list-sub-category')->with('success_msg', 'Sub Category updated successfully!');
        } else {
            return redirect('list-sub-category')->with('error_msg', 'Something went wrong while updating category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $sub_category = SubCategory::find($request->id);
        if (!$sub_category) {
            return response()->json(['error' => 'Sub Category not found.'], 404);
        }

        // $products = Product::where('sub_category_id', $request->id)->exists();
        // $subCategories = Category::where('id', $sub_category->category_id)->exists();

        // dd($products,$subCategories);
        // if ($products || $subCategories) {
        //     return response()->json(['error' => 'You cannot remove this category as it is associated with products or subcategories.'], 422);
        // }

        $sub_category->delete();

        return response()->json(['success' => true, 'message' => 'Sub Category deleted successfully.'], 200);
    }
}
