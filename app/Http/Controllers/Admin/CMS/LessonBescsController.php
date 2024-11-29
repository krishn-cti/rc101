<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\LessonBescs;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LessonBescsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LessonBescs::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('lesson_description', function ($row) {
                    $plainText = strip_tags($row->lesson_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                ->addColumn('lesson_cover_image', function ($row) {
                    if ($row->lesson_cover_image) {
                        $src = $row->lesson_cover_image;
                        $image = '<img src="' . asset('cms_images/lesson/bescs') . '/' . $src . '" alt="Bescs Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Bescs Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/lessons/bescs-edit/' . $row->id) . '">
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
                ->rawColumns(['lesson_cover_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.lessons.bescs.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.lessons.bescs.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lesson_title' => 'required|string|max:150',
            'lesson_description' => 'required',
            'lesson_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $lesson_cover_image = '';
        if ($request->hasFile('lesson_cover_image')) {
            $lesson_cover_image = $request->file('lesson_cover_image');
            $fileName = uniqid() . '.' . $lesson_cover_image->getClientOriginalExtension();
            $lesson_cover_image->move(public_path('cms_images/lesson/bescs'), $fileName);

            $lesson_cover_image = $fileName;
        }

        $isInserted = LessonBescs::insert([
            'lesson_title' => $request->lesson_title,
            'lesson_description' => $request->lesson_description,
            'lesson_cover_image' => $lesson_cover_image,
        ]);

        if ($isInserted) {
            return redirect('cms/lessons/bescs-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/lessons/bescs-list')->with('error_msg', 'Something went wrong!');
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
        $data['bescsData'] = LessonBescs::where('id', $id)->first();
        return view('admin.content_management.lessons.bescs.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'lesson_title' => 'required|string|max:150',
            'lesson_description' => 'required',
            'lesson_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingBescs = LessonBescs::where('id', $id)->first();
        if (!$existingBescs) {
            return redirect('cms/lessons/bescs-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'lesson_title' => $request->lesson_title,
            'lesson_description' => $request->lesson_description,
        ];

        if ($request->hasFile('lesson_cover_image')) {
            $lesson_cover_image = $request->file('lesson_cover_image');
            $fileName = uniqid() . '.' . $lesson_cover_image->getClientOriginalExtension();
            $lesson_cover_image->move(public_path('cms_images/lesson/bescs'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingBescs->lesson_cover_image;
            $oldImagePath = public_path('cms_images/lesson/bescs/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['lesson_cover_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = LessonBescs::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/lessons/bescs-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/lessons/bescs-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $lessonBescsInfo = LessonBescs::where('id', $request->id)->first();

        if ($lessonBescsInfo) {
            if (!empty($lessonBescsInfo->lesson_cover_image)) {
                $imagePath = public_path('cms_images/lesson/bescs/' . $lessonBescsInfo->lesson_cover_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            LessonBescs::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}