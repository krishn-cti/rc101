<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\SparcRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SparcRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SparcRule::orderBy('id', 'DESC')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('sparc_rule_description', function ($row) {
                    $plainText = strip_tags($row->sparc_rule_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                // ->addColumn('sparc_rule_description', function ($row) {
                //     return Str::limit($row->sparc_rule_description, 50, '...');
                // })
                ->addColumn('container_color', function ($row) {
                    // Return a color box and code
                    return '<div class="d-flex align-items-center">
                                <div style="width: 20px; height: 20px; background-color: ' . $row->container_color . '; border: 1px solid #ccc; margin-right: 8px;"></div>
                                <span>' . $row->container_color . '</span>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/sparc-rule-edit/' . $row->id) . '">
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
                ->rawColumns(['container_color', 'action'])
                ->make(true);
        }

        return view('admin.content_management.sparc_rules.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.sparc_rules.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sparc_rule_title' => 'required',
            'sparc_rule_description' => 'required',
        ]);

        $isInserted = SparcRule::insert([
            'sparc_rule_title' => $request->sparc_rule_title,
            'sparc_rule_description' => $request->sparc_rule_description,
            'container_color' => $request->container_color,
        ]);

        if ($isInserted) {
            return redirect('cms/sparc-rule-list')->with('success_msg', 'Sparc Rules added successfully!');
        } else {
            return redirect('cms/sparc-rule-list')->with('error_msg', 'Something went wrong!');
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
        $data['sparcRuleData'] = SparcRule::where('id', $id)->first();
        return view('admin.content_management.sparc_rules.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'sparc_rule_title' => 'required',
            'sparc_rule_description' => 'required',
        ]);

        $id = $request->id;

        // Check if the sparc rule with the provided id exists
        $existingSparcRule = SparcRule::where('id', $id)->first();
        if (!$existingSparcRule) {
            return redirect('cms/sparc-rule-list')->with('error_msg', 'Sparc Rules not found.');
        }

        // Update sparc rule fields
        $updateData = [
            'sparc_rule_title' => $request->sparc_rule_title,
            'sparc_rule_description' => $request->sparc_rule_description,
            'container_color' => $request->container_color,
        ];

        // Perform the update
        $isUpdated = SparcRule::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/sparc-rule-list')->with('success_msg', 'Sparc Rules updated successfully!');
        } else {
            return redirect('cms/sparc-rule-list')->with('error_msg', 'Failed to update sparc rule.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $sparcRuleInfo = SparcRule::where('id', $request->id)->first();

        if ($sparcRuleInfo) {
            SparcRule::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Sparc Rules deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Sparc Rules not found.'], 404);
        }
    }
}
