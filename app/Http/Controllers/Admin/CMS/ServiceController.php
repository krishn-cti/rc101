<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    //get the list of all services
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('service_image', function ($row) {
                    if ($row->service_image) {
                        $src = $row->service_image;
                        $image = '<img src="' . asset('cms_images/services') . '/' . $src . '" alt="Service Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Service Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/service-edit/' . $row->id) . '">
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
                ->rawColumns(['service_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.services.list');
    }

    // get the create page of service
    public function create()
    {
        return view('admin.content_management.services.add');
    }

    // this method is used to save service
    public function store(Request $request)
    {
        $request->validate([
            'service_title' => 'required|string|max:100',
            'service_description' => 'required',
            'service_image' => 'image'
        ]);

        // Insert data into the database using DB facade
        $service_image = '';
        if ($request->hasFile('service_image')) {
            $service_image = $request->file('service_image');
            $fileName = uniqid() . '.' . $service_image->getClientOriginalExtension();
            $service_image->move(public_path('cms_images/services'), $fileName);

            $service_image = $fileName;
        }

        $isInserted = Service::insert([
            'service_title' => $request->service_title,
            'service_description' => $request->service_description,
            'service_image' => $service_image,
            // Include other fields as needed
        ]);

        if ($isInserted) {
            return redirect('cms/service-list')->with('success_msg', 'Service added successfully!');
        } else {
            return redirect('cms/service-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit service page
    public function edit($id)
    {
        $data['serviceData'] = Service::where('id', $id)->first();
        return view('admin.content_management.services.edit', $data);
    }

    // this method is used to update service
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'service_title' => 'required|string|max:100',
            'service_description' => 'required',
            'service_image' => 'image'
        ]);

        $id = $request->id;

        // Check if the service with the provided id exists
        $existingService = Service::where('id', $id)->first();
        if (!$existingService) {
            return redirect('cms/service-list')->with('error_msg', 'Service not found.');
        }

        // Update service fields
        $updateData = [
            'service_title' => $request->service_title,
            'service_description' => $request->service_description,
        ];

        // Update service image if provided
        if ($request->hasFile('service_image')) {
            $service_image = $request->file('service_image');
            $fileName = uniqid() . '.' . $service_image->getClientOriginalExtension();
            $service_image->move(public_path('cms_images/services'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingService->service_image;
            $oldImagePath = public_path('cms_images/services/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['service_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Service::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/service-list')->with('success_msg', 'Service updated successfully!');
        } else {
            return redirect('cms/service-list')->with('error_msg', 'Failed to update service.');
        }
    }

    // this method is used to delete the service
    public function destroy(Request $request)
    {
        $serviceInfo = Service::where('id', $request->id)->first();

        if ($serviceInfo) {
            if (!empty($serviceInfo->service_image)) {
                $imagePath = public_path('cms_images/services/' . $serviceInfo->service_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            Service::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Service deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
        }
    }
}
