<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Curriculum;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::where('status', 1)
                ->latest()
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                                    <a href="' . url('curriculums/category-edit/' . $row->id) . '">
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

        return view('admin/category/list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
            'description' => 'required|string|max:255'
        ]);

        $category = new Category();
        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $category->save();

        if ($category) {
            return redirect('curriculums/category-list')->with('success_msg', 'Category added successfully!!');
        } else {
            return redirect('curriculums/category-list')->with('error_msg', 'Something went wrong!!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category, $id)
    {
        $data['category'] = Category::where('id', $id)->where('status', 1)->first();
        return view('admin.category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
            'description' => 'required|string|max:255'
        ]);

        $id = $request->id;
        $category = Category::find($id);
        if (!$category) {
            return redirect('category-list')->with('error_msg', 'Category not found.');
        }

        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $category->save();

        if ($category) {
            return redirect('curriculums/category-list')->with('success_msg', 'Category updated successfully!');
        } else {
            return redirect('curriculums/category-list')->with('error_msg', 'Something went wrong while updating category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        // $products = Product::where('category_id', $category->id)->exists();
        // $subCategories = SubCategory::where('category_id', $category->id)->exists();
        $curriculums = Curriculum::where('category_id', $category->id)->exists();

        if ($curriculums) {
            return response()->json(['error' => 'You cannot remove this category as it is associated with curriculums.'], 422);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully.'], 200);
    }
}
