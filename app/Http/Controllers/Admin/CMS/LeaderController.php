<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeaderController extends Controller
{
    //get the list of all leader
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Leader::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('profile_image', function ($row) {
                    if ($row->profile_image) {
                        $src = $row->profile_image;
                        $image = '<img src="' . asset('cms_images/leaders') . '/' . $src . '" alt="Profile Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Company Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/leader-edit/' . $row->id) . '">
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
                ->rawColumns(['profile_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.leader.list');
    }

    // get the create page of leader
    public function create()
    {
        return view('admin.content_management.leader.add');
    }

    // this method is used to save leader
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'required',
            'profile_image' => 'image'
        ]);

        // Insert data into the database using DB facade
        $profile_image = '';
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $fileName = uniqid() . '.' . $profile_image->getClientOriginalExtension();
            $profile_image->move(public_path('cms_images/leaders'), $fileName);

            $profile_image = $fileName;
        }

        $isInserted = Leader::insert([
            'name' => $request->name,
            'designation' => $request->designation,
            'about' => $request->about,
            'profile_image' => $profile_image,
            // Include other fields as needed
        ]);

        if ($isInserted) {
            return redirect('cms/leader-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/leader-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit leader page
    public function edit($id)
    {
        $data['leaderData'] = Leader::where('id', $id)->first();
        return view('admin.content_management.leader.edit', $data);
    }

    // this method is used to update leader
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'required',
            'profile_image' => 'image'
        ]);

        $id = $request->id;

        // Check if the leader with the provided id exists
        $existingLeader = Leader::where('id', $id)->first();
        if (!$existingLeader) {
            return redirect('cms/leader-list')->with('error_msg', 'Leaders not found.');
        }

        // Update leader fields
        $updateData = [
            'name' => $request->name,
            'designation' => $request->designation,
            'about' => $request->about,
        ];

        // Update profile image for leader if provided
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $fileName = uniqid() . '.' . $profile_image->getClientOriginalExtension();
            $profile_image->move(public_path('cms_images/leaders'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingLeader->profile_image;
            $oldImagePath = public_path('cms_images/leaders/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['profile_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Leader::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/leader-list')->with('success_msg', 'Data details updated successfully!');
        } else {
            return redirect('cms/leader-list')->with('error_msg', 'Failed to update data.');
        }
    }

    // this method is used to delete the leader
    public function destroy(Request $request)
    {
        $leaderInfo = Leader::where('id', $request->id)->first();

        if ($leaderInfo) {
            if (!empty($leaderInfo->profile_image)) {
                $imagePath = public_path('cms_images/leaders/' . $leaderInfo->profile_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            Leader::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
