<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\WeightAntweight;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WeightAntweightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = WeightAntweight::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('weight_class_description', function ($row) {
                    $plainText = strip_tags($row->weight_class_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                ->addColumn('weight_class_image', function ($row) {
                    if ($row->weight_class_image) {
                        $src = $row->weight_class_image;
                        $image = '<img src="' . asset('cms_images/weight/antweights') . '/' . $src . '" alt="Antweight Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Antweight Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/weight-classes/antweight-edit/' . $row->id) . '">
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
                ->rawColumns(['weight_class_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.weight_classes.antweight.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.weight_classes.antweight.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'weight_class_title' => 'required|string|max:150',
            'weight_class_description' => 'required',
            'weight_class_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $weight_class_image = '';
        if ($request->hasFile('weight_class_image')) {
            $weight_class_image = $request->file('weight_class_image');
            $fileName = uniqid() . '.' . $weight_class_image->getClientOriginalExtension();
            $weight_class_image->move(public_path('cms_images/weight/antweights'), $fileName);

            $weight_class_image = $fileName;
        }

        $isInserted = WeightAntweight::insert([
            'weight_class_title' => $request->weight_class_title,
            'weight_class_description' => $request->weight_class_description,
            'weight_class_image' => $weight_class_image,
        ]);

        if ($isInserted) {
            return redirect('cms/weight-classes/antweight-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/weight-classes/antweight-list')->with('error_msg', 'Something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['antweightData'] = WeightAntweight::where('id', $id)->first();
        return view('admin.content_management.weight_classes.antweight.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'weight_class_title' => 'required|string|max:150',
            'weight_class_description' => 'required',
            'weight_class_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingAntweight = WeightAntweight::where('id', $id)->first();
        if (!$existingAntweight) {
            return redirect('cms/weight-classes/antweight-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'weight_class_title' => $request->weight_class_title,
            'weight_class_description' => $request->weight_class_description,
        ];

        if ($request->hasFile('weight_class_image')) {
            $weight_class_image = $request->file('weight_class_image');
            $fileName = uniqid() . '.' . $weight_class_image->getClientOriginalExtension();
            $weight_class_image->move(public_path('cms_images/weight/antweights'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingAntweight->weight_class_image;
            $oldImagePath = public_path('cms_images/weight/antweights/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['weight_class_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = WeightAntweight::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/weight-classes/antweight-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/weight-classes/antweight-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $weightAntweightInfo = WeightAntweight::where('id', $request->id)->first();

        if ($weightAntweightInfo) {
            if (!empty($weightAntweightInfo->weight_class_image)) {
                $imagePath = public_path('cms_images/weight/antweights/' . $weightAntweightInfo->weight_class_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            WeightAntweight::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}