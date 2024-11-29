<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\ToolsTrade;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ToolsTradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ToolsTrade::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('tools_trade_description', function ($row) {
                    $plainText = strip_tags($row->tools_trade_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                ->addColumn('tools_trade_image', function ($row) {
                    if ($row->tools_trade_image) {
                        $src = $row->tools_trade_image;
                        $image = '<img src="' . asset('cms_images/') . '/' . $src . '" alt="Tools Trade Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Tools Trade Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/tools-trade-edit/' . $row->id) . '">
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
                ->rawColumns(['tools_trade_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.tools_trade.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.tools_trade.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tools_trade_title' => 'required|string|max:150',
            'tools_trade_description' => 'required',
            'tools_trade_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tools_trade_image = '';
        if ($request->hasFile('tools_trade_image')) {
            $tools_trade_image = $request->file('tools_trade_image');
            $fileName = uniqid() . '.' . $tools_trade_image->getClientOriginalExtension();
            $tools_trade_image->move(public_path('cms_images/'), $fileName);

            $tools_trade_image = $fileName;
        }

        $isInserted = ToolsTrade::insert([
            'tools_trade_title' => $request->tools_trade_title,
            'tools_trade_description' => $request->tools_trade_description,
            'tools_trade_image' => $tools_trade_image,
        ]);

        if ($isInserted) {
            return redirect('cms/tools-trade-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/tools-trade-list')->with('error_msg', 'Something went wrong!');
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
        $data['toolsTradeData'] = ToolsTrade::where('id', $id)->first();
        return view('admin.content_management.tools_trade.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'tools_trade_title' => 'required|string|max:150',
            'tools_trade_description' => 'required',
            'tools_trade_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingToolsTrade = ToolsTrade::where('id', $id)->first();
        if (!$existingToolsTrade) {
            return redirect('cms/tools-trade-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'tools_trade_title' => $request->tools_trade_title,
            'tools_trade_description' => $request->tools_trade_description,
        ];

        if ($request->hasFile('tools_trade_image')) {
            $tools_trade_image = $request->file('tools_trade_image');
            $fileName = uniqid() . '.' . $tools_trade_image->getClientOriginalExtension();
            $tools_trade_image->move(public_path('cms_images/'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingToolsTrade->tools_trade_image;
            $oldImagePath = public_path('cms_images/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['tools_trade_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = ToolsTrade::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/tools-trade-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/tools-trade-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $toolsTrade = ToolsTrade::where('id', $request->id)->first();

        if ($toolsTrade) {
            if (!empty($toolsTrade->tools_trade_image)) {
                $imagePath = public_path('cms_images/' . $toolsTrade->tools_trade_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            ToolsTrade::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
