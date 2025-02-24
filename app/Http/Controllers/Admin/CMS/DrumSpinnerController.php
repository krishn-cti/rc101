<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\DrumSpinner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DrumSpinnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DrumSpinner::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('description', function ($row) {
                    $plainText = strip_tags($row->description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $src = $row->image;
                        $image = '<img src="' . $src . '" alt="Drum Spinner Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Drum Spinner Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/knowledgebases/drum-spinner-edit/' . $row->id) . '">
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
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.knowledgebases.drumspinner.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.knowledgebases.drumspinner.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('cms_images'), $fileName);

            $image = $fileName;
        }

        $isInserted = DrumSpinner::insert([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
        ]);

        if ($isInserted) {
            return redirect('cms/knowledgebases/drum-spinner-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/knowledgebases/drum-spinner-list')->with('error_msg', 'Something went wrong!');
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
        $data['drumspinnerData'] = DrumSpinner::where('id', $id)->first();
        return view('admin.content_management.knowledgebases.drumspinner.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'title' => 'required|string|max:150',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingData = DrumSpinner::where('id', $id)->first();
        if (!$existingData) {
            return redirect('cms/knowledgebases/drum-spinner-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('cms_images'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingData->image;
            $oldImagePath = public_path('cms_images/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['image'] = $fileName;
        }

        // Perform the update
        $isUpdated = DrumSpinner::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/knowledgebases/drum-spinner-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/knowledgebases/drum-spinner-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $result = DrumSpinner::where('id', $request->id)->first();

        if ($result) {
            if (!empty($result->image)) {
                $imagePath = public_path('cms_images/' . $result->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            DrumSpinner::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
