<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::whereNotNull('google_id')
                ->where('role_id', 3)
                ->where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('profile_image', function ($row) {
                    if (basename($row->profile_image) == "no-user.webp" && $row->google_profile_image != null) {
                        return '<img src="' . $row->google_profile_image . '" alt="Profile Image" class="td_img_50">';
                    } else {
                        return '<img src="' . $row->profile_image . '" alt="Profile Image" class="td_img_50">';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                // for edit action button

                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('users/edit-teacher/' . $row->id) . '">
                            <lord-icon data-bs-toggle="modal" data-bs-target="#ct_edit_product" src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" colors="primary:#333333,secondary:#333333" style="width:20px;height:20px">
                            </lord-icon>
                        </a>
                        <a href="javascript:;"  title="Delete" onclick="deleteConfirm(' . $row->id . ')">
                            <lord-icon src="https://cdn.lordicon.com/drxwpfop.json" trigger="hover" colors="primary:#ff0000,secondary:#ff0000" style="width:20px;height:20px">
                            </lord-icon>
                        </a>
                     </div>';
                    return $btn;
                })
                ->rawColumns(['profile_image', 'action'])
                ->make(true);
        }

        return view('admin.teacher.list');
    }

    public function edit($id)
    {
        $data['user'] = User::where('id', $id)->first();
        return view('admin.teacher.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email,' . $request->id,
            'number' => ['required', 'regex:/^[0-9]{7,15}$/'],
        ], [
            'number.regex' => 'Please enter a valid phone number with 7 to 15 digits.',
        ]);

        $id = $request->id;
        $teacher = User::find($id);

        if (!$teacher) {
            return redirect('users/list-teacher')->with('error_msg', 'User not found.');
        }

        $teacher->name = $request->name;
        $teacher->email = $request->email;
        $teacher->number = $request->number;

        if ($request->hasFile('profile_image')) {
            // Check if there's an existing image and delete it
            $existingImage = $teacher->getRawOriginal('profile_image');
            $existingImagePath = public_path('profile_images/' . $existingImage);

            if ($existingImage && file_exists($existingImagePath)) {
                unlink($existingImagePath);
            }

            // Save the new image
            $profile_image = $request->file('profile_image');
            $fileName = uniqid() . '.' . $profile_image->getClientOriginalExtension();
            $profile_image->move(public_path('profile_images'), $fileName);
            $teacher->profile_image = $fileName;
        }

        if ($teacher->save()) {
            return redirect('users/list-teacher')->with('success_msg', 'User updated successfully!');
        } else {
            return redirect('users/list-teacher')->with('error_msg', 'Something went wrong while updating teacher.');
        }
    }

    public function destroy(Request $request)
    {
        $teacher = User::where('id', $request->id)->first();
        if (!empty($teacher->profile_image)) {
            $imagePath = public_path('profile_images/' . $teacher->profile_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            User::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Teacher deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
