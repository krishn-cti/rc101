<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Embed;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmbedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Embed::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('embed_link', function ($row) {
                    return '<span style="width: 100%;max-width:350px;display:block">' . $row->embed_link . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/embeds-edit/' . $row->id) . '">
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
                ->rawColumns(['embed_link', 'action'])
                ->make(true);
        }

        return view('admin.content_management.embeds.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.embeds.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'type' => 'required|in:doc,slide',
            'embed_link' => 'required|url',
        ]);

        $isInserted = Embed::insert([
            'title' => $request->title,
            'type' => $request->type,
            'embed_link' => $request->embed_link,
        ]);

        if ($isInserted) {
            return redirect('cms/embeds-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/embeds-list')->with('error_msg', 'Something went wrong!');
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
        $data['embedsData'] = Embed::where('id', $id)->first();
        return view('admin.content_management.embeds.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'title' => 'required|string|max:150',
            'type' => 'required|in:doc,slide',
            'embed_link' => 'required|url',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingData = Embed::where('id', $id)->first();
        if (!$existingData) {
            return redirect('cms/embeds-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'title' => $request->title,
            'type' => $request->type,
            'embed_link' => $request->embed_link,
        ];

        // Perform the update
        $isUpdated = Embed::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/embeds-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/embeds-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $result = Embed::where('id', $request->id)->first();

        if ($result) {
            Embed::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
