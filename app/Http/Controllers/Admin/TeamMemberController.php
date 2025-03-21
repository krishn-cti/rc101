<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('role_id', 4)
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
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('users/edit-member/' . $row->id) . '">
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

        return view('admin.team_member.list');
    }

    public function create()
    {
        return view('admin.team_member.add');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'number' => 'required'
        ]);

        $user = new User();
        $user->password = '12345678';
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->role_id = 4;
        $user->show_password = '12345678';
        $user->password = Hash::make('12345678');
        $user->email_verified_at = date('Y-m-d h:i:s');

        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $fileName = uniqid() . '.' . $profile_image->getClientOriginalExtension();
            $profile_image->move(public_path('profile_images'), $fileName);

            $user->profile_image = $fileName;
        }

        if ($user->save()) {
            Mail::to($user->email)->send(new UserWelcomeMail($user));
            return redirect('users/list-member')->with('success_msg', 'User added successfully!!');
        } else {
            return redirect('users/list-member')->with('error_msg', 'Something went wrong!!');
        }
        return redirect('users/list-member');
    }

    public function edit($id)
    {
        $data['member'] = User::where('id', $id)->first();
        return view('admin.team_member.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'number' => 'required',
            // Add more validation rules for other fields if needed
        ]);

        $id = $request->id;
        $user = User::find($id);

        if (!$user) {
            return redirect('users/list-member')->with('error_msg', 'User not found.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;

        if ($request->hasFile('profile_image')) {
            // Check if there's an existing image and delete it
            $existingImage = $user->getRawOriginal('profile_image');
            $existingImagePath = public_path('profile_images/' . $existingImage);

            if ($existingImage && file_exists($existingImagePath)) {
                unlink($existingImagePath);
            }

            // Save the new image
            $profile_image = $request->file('profile_image');
            $fileName = uniqid() . '.' . $profile_image->getClientOriginalExtension();
            $profile_image->move(public_path('profile_images'), $fileName);
            $user->profile_image = $fileName;
        }

        if ($user->save()) {
            return redirect('users/list-member')->with('success_msg', 'Member updated successfully!');
        } else {
            return redirect('users/list-member')->with('error_msg', 'Something went wrong while updating member.');
        }
    }


    // public function destroy(Request $request)
    // {
    //     $member = User::where('role_id', 4)->where('id', $request->id)->first();
    //     if (!empty($member->profile_image)) {
    //         $imagePath = public_path('profile_images/' . $member->profile_image);
    //         if (file_exists($imagePath)) {
    //             unlink($imagePath);
    //         }
    //         User::where('id', $request->id)->delete();
    //         return response()->json(['success' => true, 'message' => 'Member deleted successfully.'], 200);
    //     }else {
    //         return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
    //     }
    // }
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $member = User::where('role_id', 4)->where('id', $request->id)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.',
            ], 404);
        }

        // Check and delete profile image if it exists
        if (!empty($member->profile_image_path)) { // Using accessor
            $imagePath = public_path($member->profile_image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the user
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully.',
        ], 200);
    }
}
