<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PresentationController extends Controller
{
    //get the list of all leagues
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Presentation::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('presentation_cover_image', function ($row) {
                    if ($row->presentation_cover_image) {
                        $src = $row->presentation_cover_image;
                        $image = '<img src="' . asset('cms_images/leagues/presentations') . '/' . $src . '" alt="Presentation Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Presentation Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/presentation-edit/' . $row->id) . '">
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
                ->rawColumns(['presentation_cover_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.leagues.presentations.list');
    }

    // get the create page of presentation
    public function create()
    {
        return view('admin.content_management.leagues.presentations.add');
    }

    // this method is used to save presentation
    public function store(Request $request)
    {
        $request->validate([
            'presentation_title' => 'required|string|max:255',
            'presentation_cover_image' => 'image',
        ]);

        // Insert data into the database using DB facade
        $presentation_cover_image = '';
        if ($request->hasFile('presentation_cover_image')) {
            $presentation_cover_image = $request->file('presentation_cover_image');
            $fileName = uniqid() . '.' . $presentation_cover_image->getClientOriginalExtension();
            $presentation_cover_image->move(public_path('cms_images/leagues/presentations'), $fileName);

            $presentation_cover_image = $fileName;
        }

        $isInserted = Presentation::insert([
            'presentation_title' => $request->presentation_title,
            'presentation_youtube_link' => $request->presentation_youtube_link,
            'presentation_cover_image' => $presentation_cover_image,
        ]);

        if ($isInserted) {
            return redirect('cms/presentation-list')->with('success_msg', 'Presentation added successfully!');
        } else {
            return redirect('cms/presentation-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit presentation page
    public function edit($id)
    {
        $data['presentationData'] = Presentation::where('id', $id)->first();
        return view('admin.content_management.leagues.presentations.edit', $data);
    }

    // this method is used to update presentation
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'presentation_title' => 'required|string|max:255',
            'presentation_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the presentation with the provided id exists
        $existingPresentation = Presentation::where('id', $id)->first();
        if (!$existingPresentation) {
            return redirect('cms/presentation-list')->with('error_msg', 'Presentation not found.');
        }

        // Update presentation fields
        $updateData = [
            'presentation_title' => $request->presentation_title,
            'presentation_youtube_link' => $request->presentation_youtube_link,
        ];

        // Update presentation image if provided
        if ($request->hasFile('presentation_cover_image')) {
            $presentation_cover_image = $request->file('presentation_cover_image');
            $fileName = uniqid() . '.' . $presentation_cover_image->getClientOriginalExtension();
            $presentation_cover_image->move(public_path('cms_images/leagues/presentations'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingPresentation->presentation_cover_image;
            $oldImagePath = public_path('cms_images/leagues/presentations/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['presentation_cover_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Presentation::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/presentation-list')->with('success_msg', 'Presentation updated successfully!');
        } else {
            return redirect('cms/presentation-list')->with('error_msg', 'Failed to update presentation.');
        }
    }


    // this method is used to delete the service
    public function destroy(Request $request)
    {
        $presentationInfo = Presentation::where('id', $request->id)->first();

        if ($presentationInfo) {
            if (!empty($presentationInfo->presentation_cover_image)) {
                $imagePath = public_path('cms_images/leagues/presentations/' . $presentationInfo->presentation_cover_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            Presentation::where('id', $request->id)->delete();

            return response()->json(['success' => true, 'message' => 'Presentation deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Presentation not found.'], 404);
        }
    }
}
