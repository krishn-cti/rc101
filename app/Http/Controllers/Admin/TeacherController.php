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
                // <a href="' . url('edit-teacher/' . $row->id) . '">
                //             <lord-icon data-bs-toggle="modal" data-bs-target="#ct_edit_product" src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" colors="primary:#333333,secondary:#333333" style="width:20px;height:20px">
                //             </lord-icon>
                //         </a>
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
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
}
