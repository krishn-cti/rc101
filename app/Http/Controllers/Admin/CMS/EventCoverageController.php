<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\EventCoverage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EventCoverageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EventCoverage::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('event_coverage_description', function ($row) {
                    $plainText = strip_tags($row->event_coverage_description);
                    return strlen($plainText) > 50
                        ? substr($plainText, 0, 50) . '...'
                        : $plainText;
                })
                ->addColumn('event_coverage_link', function ($row) {
                    return $row->event_coverage_link ?? "#N/A";
                })
                ->addColumn('event_coverage_image', function ($row) {
                    if ($row->event_coverage_image) {
                        $src = $row->event_coverage_image;
                        $image = '<img src="' . asset('cms_images/') . '/' . $src . '" alt="Event Coverage Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Event Coverage Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/event-coverage-edit/' . $row->id) . '">
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
                ->rawColumns(['event_coverage_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.event_coverage.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.content_management.event_coverage.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_coverage_title' => 'required|string|max:150',
            'event_coverage_description' => 'required',
            'event_coverage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_coverage_link' => 'nullable|string|max|255'
        ]);

        $event_coverage_image = '';
        if ($request->hasFile('event_coverage_image')) {
            $event_coverage_image = $request->file('event_coverage_image');
            $fileName = uniqid() . '.' . $event_coverage_image->getClientOriginalExtension();
            $event_coverage_image->move(public_path('cms_images/'), $fileName);

            $event_coverage_image = $fileName;
        }

        $isInserted = EventCoverage::insert([
            'event_coverage_title' => $request->event_coverage_title,
            'event_coverage_description' => $request->event_coverage_description,
            'event_coverage_image' => $event_coverage_image,
            'event_coverage_link' => $request->event_coverage_link,
        ]);

        if ($isInserted) {
            return redirect('cms/event-coverage-list')->with('success_msg', 'Data added successfully!');
        } else {
            return redirect('cms/event-coverage-list')->with('error_msg', 'Something went wrong!');
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
        $data['eventCoverageData'] = EventCoverage::where('id', $id)->first();
        return view('admin.content_management.event_coverage.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'event_coverage_title' => 'required|string|max:150',
            'event_coverage_description' => 'required',
            'event_coverage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_coverage_link' => 'nullable|string|max|255'
        ]);

        $id = $request->id;

        // Check if the provided id exists
        $existingEventCoverage = EventCoverage::where('id', $id)->first();
        if (!$existingEventCoverage) {
            return redirect('cms/event-coverage-list')->with('error_msg', 'Data not found.');
        }

        // Update fields
        $updateData = [
            'event_coverage_title' => $request->event_coverage_title,
            'event_coverage_description' => $request->event_coverage_description,
            'event_coverage_link' => $request->event_coverage_link,
        ];

        if ($request->hasFile('event_coverage_image')) {
            $event_coverage_image = $request->file('event_coverage_image');
            $fileName = uniqid() . '.' . $event_coverage_image->getClientOriginalExtension();
            $event_coverage_image->move(public_path('cms_images/'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingEventCoverage->event_coverage_image;
            $oldImagePath = public_path('cms_images/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['event_coverage_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = EventCoverage::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/event-coverage-list')->with('success_msg', 'Data updated successfully!');
        } else {
            return redirect('cms/event-coverage-list')->with('error_msg', 'Failed to update data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $eventCoverage = EventCoverage::where('id', $request->id)->first();

        if ($eventCoverage) {
            if (!empty($eventCoverage->event_coverage_image)) {
                $imagePath = public_path('cms_images/' . $eventCoverage->event_coverage_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            EventCoverage::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found.'], 404);
        }
    }
}
