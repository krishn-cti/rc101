<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('role_id', 2)
                ->where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('profile_image', function ($row) {
                    return '<img src="' . $row->profile_image . '" alt="Profile Image" class="td_img_50">';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('edit-user/' . $row->id) . '">
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

        return view('admin.user.list');
    }

    public function create()
    {
        return view('admin.user.add');
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
            return redirect('list-user')->with('success_msg', 'User added successfully!!');
        } else {
            return redirect('list-user')->with('error_msg', 'Something went wrong!!');
        }
        return redirect('list-user');
    }

    public function edit($id)
    {
        $data['user'] = User::where('id', $id)->first();
        return view('admin.user.edit', $data);
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
            return redirect('list-user')->with('error_msg', 'User not found.');
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
            return redirect('list-user')->with('success_msg', 'User updated successfully!');
        } else {
            return redirect('list-user')->with('error_msg', 'Something went wrong while updating user.');
        }
    }


    public function destroy(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.'], 200);
    }
}
