<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PartnerController extends Controller
{
    //get the list of all partner
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Partner::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('company_image', function ($row) {
                    if ($row->company_image) {
                        $src = $row->company_image;
                        $image = '<img src="' . asset('cms_images/partners') . '/' . $src . '" alt="Company Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Company Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/partner-edit/' . $row->id) . '">
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
                ->rawColumns(['company_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.partner.list');
    }

    // get the create page of partner
    public function create()
    {
        return view('admin.content_management.partner.add');
    }

    // this method is used to save partner
    public function store(Request $request)
    {
        $request->validate([
            'company_title' => 'required|string|max:100',
            'web_link' => 'required',
            'company_image' => 'image'
        ]);

        // Insert data into the database using DB facade
        $company_image = '';
        if ($request->hasFile('company_image')) {
            $company_image = $request->file('company_image');
            $fileName = uniqid() . '.' . $company_image->getClientOriginalExtension();
            $company_image->move(public_path('cms_images/partners'), $fileName);

            $company_image = $fileName;
        }

        $isInserted = Partner::insert([
            'company_title' => $request->company_title,
            'web_link' => $request->web_link,
            'company_image' => $company_image,
            // Include other fields as needed
        ]);

        if ($isInserted) {
            return redirect('cms/partner-list')->with('success_msg', 'Company details added successfully!');
        } else {
            return redirect('cms/partner-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit partner page
    public function edit($id)
    {
        $data['partnerData'] = Partner::where('id', $id)->first();
        return view('admin.content_management.partner.edit', $data);
    }

    // this method is used to update partner
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'company_title' => 'required|string|max:100',
            'web_link' => 'required',
            'company_image' => 'image'
        ]);

        $id = $request->id;

        // Check if the partner with the provided id exists
        $existingPartner = Partner::where('id', $id)->first();
        if (!$existingPartner) {
            return redirect('cms/partner-list')->with('error_msg', 'Partners not found.');
        }

        // Update partner fields
        $updateData = [
            'company_title' => $request->company_title,
            'web_link' => $request->web_link,
        ];

        // Update company image if provided
        if ($request->hasFile('company_image')) {
            $company_image = $request->file('company_image');
            $fileName = uniqid() . '.' . $company_image->getClientOriginalExtension();
            $company_image->move(public_path('cms_images/partners'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingPartner->company_image;
            $oldImagePath = public_path('cms_images/partners/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['company_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Partner::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/partner-list')->with('success_msg', 'Company details updated successfully!');
        } else {
            return redirect('cms/partner-list')->with('error_msg', 'Failed to update Company details.');
        }
    }

    // this method is used to delete the partner
    public function destroy(Request $request)
    {
        $companyInfo = Partner::where('id', $request->id)->first();

        if ($companyInfo) {
            if (!empty($companyInfo->company_image)) {
                $imagePath = public_path('cms_images/partners/' . $companyInfo->company_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            Partner::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Company deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Company not found.'], 404);
        }
    }
}
