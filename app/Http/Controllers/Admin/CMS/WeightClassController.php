<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\WeightClass;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WeightClassController extends Controller
{
    //get the list of all weight classes
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = WeightClass::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('weight_class_description', function ($row) {
                    $plainText = strip_tags($row->weight_class_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                // ->addColumn('weight_class_description', function ($row) {
                //     return Str::limit($row->weight_class_description, 50, '...');
                // })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/weight-class-edit/' . $row->id) . '">
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
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.content_management.weight_classes.list');
    }

    // get the create page of weight class
    public function create()
    {
        return view('admin.content_management.weight_classes.add');
    }

    // this method is used to save weight class
    public function store(Request $request)
    {
        $request->validate([
            'weight_class_title' => 'required',
            'weight_class_description' => 'required',
        ]);

        $isInserted = WeightClass::insert([
            'weight_class_title' => $request->weight_class_title,
            'weight_class_description' => $request->weight_class_description,
        ]);

        if ($isInserted) {
            return redirect('cms/weight-class-list')->with('success_msg', 'Weight Class added successfully!');
        } else {
            return redirect('cms/weight-class-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit weight class page
    public function edit($id)
    {
        $data['weightClassData'] = WeightClass::where('id', $id)->first();
        return view('admin.content_management.weight_classes.edit', $data);
    }

    // this method is used to update weight class
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'weight_class_title' => 'required',
            'weight_class_description' => 'required',
        ]);

        $id = $request->id;

        // Check if the weight class with the provided id exists
        $existingWeightClass = WeightClass::where('id', $id)->first();
        if (!$existingWeightClass) {
            return redirect('cms/weight-class-list')->with('error_msg', 'Weight Class not found.');
        }

        // Update weight class fields
        $updateData = [
            'weight_class_title' => $request->weight_class_title,
            'weight_class_description' => $request->weight_class_description,
        ];

        // Perform the update
        $isUpdated = WeightClass::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/weight-class-list')->with('success_msg', 'Weight Class updated successfully!');
        } else {
            return redirect('cms/weight-class-list')->with('error_msg', 'Failed to update weight class.');
        }
    }

    // this method is used to delete the weight class
    public function destroy(Request $request)
    {
        $weightClassInfo = WeightClass::where('id', $request->id)->first();

        if ($weightClassInfo) {
            WeightClass::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Weight Class deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Weight Class not found.'], 404);
        }
    }
}
