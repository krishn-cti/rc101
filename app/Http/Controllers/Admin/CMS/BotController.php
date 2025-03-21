<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Models\BotType;
use App\Models\User;
use App\Models\WeightClassCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BotController extends Controller
{
    //get the list of all bot
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Bot::orderBy('id', 'DESC')->with(['botType', 'weightClass', 'createdBy'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $src = $row->image;
                        $image = '<img src="' . $src . '" alt="Profile Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Company Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('bot_type', function ($row) {
                    return optional($row->botType)->name ?? "#N/A";
                })                
                ->addColumn('weight_class', function ($row) {
                    return optional($row->weightClass)->name ?? "#N/A";
                })                
                ->addColumn('created_by', function ($row) {
                    return optional($row->createdBy)->name ?? "Admin";
                })                
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/bot-edit/' . $row->id) . '">
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
                ->rawColumns(['image', 'bot_type', 'weight_class', 'action'])
                ->make(true);
        }

        return view('admin.content_management.bots.list');
    }

    // get the create page of bot
    public function create()
    {
        $data['botTypes'] = BotType::where('status', 1)->get();
        $data['weightClassCategories'] = WeightClassCategory::where('status', 1)->get();
        $data['teamMembers'] = User::where('role_id', 4)->select('id', 'name')->get();
        return view('admin.content_management.bots.add', $data);
    }

    // this method is used to save bot
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255', 
            'image' => 'image'
        ]);

        // Insert data into the database using DB facade
        $image = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('cms_images/bots'), $fileName);

            $image = $fileName;
        }

        $isInserted = Bot::insert([
            'name' => $request->name,
            'bot_type_id' => $request->bot_type_id,
            'design_type' => $request->design_type,
            'weight_class_id' => $request->weight_class_id,
            'start_date' => $request->start_date,
            'description' => $request->description,
            'image' => $image,
            'created_by' => $request->created_by,
            // Include other fields as needed
        ]);

        if ($isInserted) {
            return redirect('cms/bot-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/bot-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit bot page
    public function edit($id)
    {
        $data['botTypes'] = BotType::where('status', 1)->get();
        $data['weightClassCategories'] = WeightClassCategory::where('status', 1)->get();
        $data['teamMembers'] = User::where('role_id', 4)->select('id', 'name')->get();
        $data['botData'] = Bot::where('id', $id)->first();
        return view('admin.content_management.bots.edit', $data);
    }

    // this method is used to update bot
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'image' => 'image'
        ]);

        $id = $request->id;

        // Check if the bot with the provided id exists
        $existingBot = Bot::where('id', $id)->first();
        if (!$existingBot) {
            return redirect('cms/bot-list')->with('error_msg', 'Bots not found.');
        }

        // Update bot fields
        $updateData = [
            'name' => $request->name ?? $existingBot->name,
            'bot_type_id' => $request->bot_type_id ?? $existingBot->bot_type_id,
            'design_type' => $request->design_type ?? $existingBot->design_type,
            'weight_class_id' => $request->weight_class_id ?? $existingBot->weight_class_id,
            'start_date' => $request->start_date ?? $existingBot->start_date,
            'description' => $request->description ?? $existingBot->description,
            'created_by' => $request->created_by ?? $existingBot->created_by,
        ];

        // Update profile image for bot if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('cms_images/bots'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingBot->image;
            $oldImagePath = public_path('cms_images/bots/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Bot::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/bot-list')->with('success_msg', 'Data details updated successfully!');
        } else {
            return redirect('cms/bot-list')->with('error_msg', 'Failed to update data.');
        }
    }

    // this method is used to delete the bot
    public function destroy(Request $request)
    {
        $botInfo = Bot::where('id', $request->id)->first();

        if ($botInfo) {
            if (!empty($botInfo->image)) {
                $imagePath = public_path('cms_images/bots/' . $botInfo->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            Bot::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
