<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Curriculum;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Curriculum::with('category')->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('embed_link', function ($row) {
                    return '<span style="width: 100%;max-width:350px;display:block">' . $row->embed_link . '</span>';
                })
                ->addColumn('file_type', function ($row) {
                    $types = [
                        'slide_decks' => 'Slide Decks',
                        'student_pages' => 'Student Pages',
                        'instructions' => 'Instructions',
                        'samples' => 'Samples',
                    ];

                    return $types[$row->file_type] ?? ucfirst(str_replace('_', ' ', $row->file_type));
                })
                // ->addColumn('category', function ($row) {
                //     return $row->category ?? ucfirst($row->category->category_name);
                // })

                ->addColumn('category', function ($row) {
                    return $row->category ? ucfirst($row->category->category_name) : '#N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('curriculums/unit-edit/' . $row->id) . '">
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
                ->rawColumns(['embed_link', 'category', 'action'])
                ->make(true);
        }

        return view('admin.curriculums.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['categoryData'] = Category::all();
        return view('admin.curriculums.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|numeric',
            'title' => 'required|string|max:150',
            'type' => 'required|in:doc,slide',
            'file_type' => 'required|in:slide_decks,student_pages,instructions,samples',
            'embed_link' => 'required|url',
        ]);

        $isInserted = Curriculum::insert([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'file_type' => $request->file_type,
            'embed_link' => $request->embed_link,
        ]);

        if ($isInserted) {
            return redirect('curriculums/unit-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('curriculums/unit-list')->with('error_msg', 'Something went wrong!');
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
        $data['categoryData'] = Category::get();
        $data['curriculumsData'] = Curriculum::where('id', $id)->first();
        return view('admin.curriculums.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'category_id' => 'required|numeric',
            'title' => 'required|string|max:150',
            'type' => 'required|in:doc,slide',
            'file_type' => 'required|in:slide_decks,student_pages,instructions,samples',
            'embed_link' => 'required|url',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingData = Curriculum::where('id', $id)->first();
        if (!$existingData) {
            return redirect('curriculums/unit-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'title' => $request->title,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'file_type' => $request->file_type,
            'embed_link' => $request->embed_link,
        ];

        // Perform the update
        $isUpdated = Curriculum::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('curriculums/unit-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('curriculums/unit-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $result = Curriculum::where('id', $request->id)->first();

        if ($result) {
            Curriculum::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
