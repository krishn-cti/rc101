<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TournamentController extends Controller
{
    //get the list of all leagues
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tournament::orderBy('id', 'DESC')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('serial_number', function ($row) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('banner_image', function ($row) {
                    if ($row->banner_image) {
                        $src = $row->banner_image;
                        $image = '<img src="' . asset('cms_images/leagues/tournaments') . '/' . $src . '" alt="Tournament Image" class="td_img_50">';
                    } else {
                        $image = '<img src="' . asset('admin/img/shop-img/no_image.png') . '" alt="Tournament Image" class="td_img_50">';
                    }
                    return $image;
                })
                ->addColumn('start_date', function ($row) {
                    return \Carbon\Carbon::parse($row->start_date)->format('d-m-Y');
                })
                ->addColumn('end_date', function ($row) {
                    return \Carbon\Carbon::parse($row->end_date)->format('d-m-Y');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-3">
                        <a href="' . url('cms/tournament-edit/' . $row->id) . '">
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
                ->rawColumns(['banner_image', 'action'])
                ->make(true);
        }

        return view('admin.content_management.leagues.tournaments.list');
    }

    // get the create page of tournament
    public function create()
    {
        return view('admin.content_management.leagues.tournaments.add');
    }

    // this method is used to save tournament
    public function store(Request $request)
    {
        $request->validate([
            'tournament_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $banner_image = '';
        if ($request->hasFile('banner_image')) {
            $banner_image = $request->file('banner_image');
            $fileName = uniqid() . '.' . $banner_image->getClientOriginalExtension();
            $banner_image->move(public_path('cms_images/leagues/tournaments'), $fileName);

            $banner_image = $fileName;
        }

        $isInserted = Tournament::insert([
            'tournament_title' => $request->tournament_title,
            'tournament_desc' => $request->tournament_desc,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'banner_image' => $banner_image,
            // Include other fields as needed
        ]);

        if ($isInserted) {
            return redirect('cms/tournament-list')->with('success_msg', 'Tournament added successfully!');
        } else {
            return redirect('cms/tournament-list')->with('error_msg', 'Something went wrong!');
        }
    }

    // get the edit tournament page
    public function edit($id)
    {
        $data['tournamentData'] = Tournament::where('id', $id)->first();
        return view('admin.content_management.leagues.tournaments.edit', $data);
    }

    // this method is used to update tournament
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required', // Ensure an id is provided for updating
            'tournament_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $id = $request->id;

        // Check if the tournament with the provided id exists
        $existingTournament = Tournament::where('id', $id)->first();
        if (!$existingTournament) {
            return redirect('cms/tournament-list')->with('error_msg', 'Tournament not found.');
        }

        // Update tournament fields
        $updateData = [
            'tournament_title' => $request->tournament_title,
            'tournament_desc' => $request->tournament_desc,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        // Update tournament image if provided
        if ($request->hasFile('banner_image')) {
            $banner_image = $request->file('banner_image');
            $fileName = uniqid() . '.' . $banner_image->getClientOriginalExtension();
            $banner_image->move(public_path('cms_images/leagues/tournaments'), $fileName);

            // Delete the old image if it exists
            $oldImage = $existingTournament->banner_image;
            $oldImagePath = public_path('cms_images/leagues/tournaments/' . $oldImage);
            if (file_exists($oldImagePath) && !empty($oldImage)) {
                unlink($oldImagePath);
            }

            $updateData['banner_image'] = $fileName;
        }

        // Perform the update
        $isUpdated = Tournament::where('id', $id)->update($updateData);

        if ($isUpdated) {
            return redirect('cms/tournament-list')->with('success_msg', 'Tournament updated successfully!');
        } else {
            return redirect('cms/tournament-list')->with('error_msg', 'Failed to update tournament.');
        }
    }


    // this method is used to delete the tournament
    public function destroy(Request $request)
    {
        $tournamentInfo = Tournament::where('id', $request->id)->first();

        if ($tournamentInfo) {
            if (!empty($tournamentInfo->banner_image)) {
                $imagePath = public_path('cms_images/leagues/tournaments/' . $tournamentInfo->banner_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            Tournament::where('id', $request->id)->delete();

            return response()->json(['success' => true, 'message' => 'Tournament deleted successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Tournament not found.'], 404);
        }
    }
}
