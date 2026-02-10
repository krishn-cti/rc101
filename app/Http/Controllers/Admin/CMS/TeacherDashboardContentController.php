<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\TeacherDashboardContent;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeacherDashboardContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = TeacherDashboardContent::orderBy('id', 'DESC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('link', function ($row) {
                    return '<a href="' . $row->link . '" target="_blank"><span style="max-width:300px;display:block">' . $row->link . '</span></a>';
                })
                ->addColumn('action', function ($row) {
                    return '
                <div class="d-flex align-items-center gap-3">
                    <a href="' . url('cms/dashboard-content-edit/' . $row->id) . '">
                        <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json"
                            trigger="hover"
                            colors="primary:#333333,secondary:#333333"
                            style="width:20px;height:20px">
                        </lord-icon>
                    </a>
                    <a href="javascript:;" onclick="deleteConfirm(' . $row->id . ')">
                        <lord-icon src="https://cdn.lordicon.com/drxwpfop.json"
                            trigger="hover"
                            colors="primary:#ff0000,secondary:#ff0000"
                            style="width:20px;height:20px">
                        </lord-icon>
                    </a>
                </div>';
                })
                ->rawColumns(['link', 'action'])
                ->make(true);
        }

        return view('admin.content_management.dashboard_content.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.dashboard_content.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:255'
        ]);

        $isInserted = TeacherDashboardContent::insert([
            'title' => $request->title,
            'link' => $request->link
        ]);

        if ($isInserted) {
            return redirect('cms/dashboard-content-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/dashboard-content-list')->with('error_msg', 'Something went wrong!');
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
        $data['dashboardContentData'] = TeacherDashboardContent::where('id', $id)->first();
        return view('admin.content_management.dashboard_content.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:255',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingData = TeacherDashboardContent::where('id', $id)->first();
        if (!$existingData) {
            return redirect('cms/dashboard-content-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'title' => $request->title,
            'link' => $request->link
        ];

        // Perform the update
        $isUpdated = TeacherDashboardContent::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/dashboard-content-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/dashboard-content-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $result = TeacherDashboardContent::where('id', $request->id)->first();

        if ($result) {
            TeacherDashboardContent::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
