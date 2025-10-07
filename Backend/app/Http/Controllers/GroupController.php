<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\GroupsDocuments;
use App\Models\GroupsPivotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function groupList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Groups::with('documents','users')
                    ->withCount('groupPivote')
                    ->orderBy('id', 'desc');
                if ($request->filled('created_by')) {
                    $data->where('created_by', $request->created_by);
                }
                if ($request->filled('status')) {
                    $data->where('status', $request->status);
                }
                if ($request->filled('role')) {
                    $data->whereHas('users', function ($q) use ($request) {
                        $q->whereHas('roles', function ($q) use ($request) {
                            $q->where('name', $request->role);
                        });
                    });
                }
                $count_total1 = $data->count();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->with([
                        "recordsTotal" => $count_total1,
                        "recordsFiltered" => $count_total1,
                    ])
                    ->addColumn('created_by', function ($row) {
                        return $row->users->name;
                    })

                    ->addColumn('groupRequest', function ($row) {
                        $badge = '<span class="badge badge-light" title="'.$row->group_pivote_count.' count click here to go to list.">';
                        $badge .= '<a target="_blank" href="'.url('/group-request-list/'.$row->id).'"><i class="menu-icon mdi mdi-link-variant"></i>&nbsp;&nbsp;'.$row->group_pivote_count.'</a>';
                        $badge .= '</span>';
                        return $badge;
                    })

                    ->addColumn('action', function ($row) {
                        $btn = '<a href="/groupEdit/'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['action','created_by','groupRequest'])
                    ->make(true);
            }
            return view('group.index');
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function create(){
        return view('group.add');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation rules
            $rules = [
                'name' => 'required|string',
                'group_document' => 'required|max:2048', // max 2MB
            ];

            // Validate request
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
            }

            // Create group record
            $group = Groups::create([
                'name' => $request->input('name'),
                'created_by' => Auth::user()->id,
                'status' => $request->input('status', 0), // Assuming 'status' is part of the form data
            ]);

            // Handle file uploads
            if ($request->hasFile('group_document')) {
                $basePath = storage_path('app/public/group_documents');
                $thumbnailPath = $basePath . '/thumbnail';

                // Create thumbnail directory if it doesn't exist
                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                foreach ($request->file('group_document') as $file) {
                    $path = $file->store('group_documents', 'public');

                    $groupDocument = new GroupsDocuments();
                    $groupDocument->group_document = $path;
                    $groupDocument->group_id = $group->id;
                    $groupDocument->save();
                }
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Record saved successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show(Request $request, $user_id) {
        $target_user = Groups::find($user_id);

        return view('users.sellers.show', [
            'user' => $target_user
        ]);
    }

    public function getEdit(Request $request, $id) {
        $group = Groups::with('documents','users')->find($id);
        return view('group.edit',compact('group'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation rules
            $rules = [
                'name' => 'required|string',
            ];

            // Validate request
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
            }

            // Update group record
            $group = Groups::where('id' ,$request->group_id)->first();
            $group->name = $request->input('name');
            $group->status = $request->input('status');
            $group->save();

            if ($request->hasFile('group_document')) {
                GroupsDocuments::where('group_id',$request->group_id)->delete();
                foreach ($request->file('group_document') as $file) {
                    $path = $file->store('group_documents', 'public'); // Store file in storage/app/public/group_documents
                    $groupDocument = new GroupsDocuments();
                    $groupDocument->group_document = $path;
                    $groupDocument->group_id = $group->id;
                    $groupDocument->save();
                }
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Record saved successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }

    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $group = Groups::findOrFail($id); // Find the group by ID
            $group->delete(); // Perform the deletion

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Group deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $user = Groups::where('id',$request->id)->first();
            $user->status = $request->status;
            $user->save();
            return response()->json(['status' => true, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    public function updateGroupMemberStatus(Request $request)
    {
        try {
            $user = GroupsPivotes::where('id',$request->id)->first();
            $user->status = $request->status;
            $user->save();
            return response()->json(['status' => true, 'message' => 'Group Join Request Status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



    public function groupRequestList(Request $request,$group_id){
        try {
            if ($request->ajax()) {
                $data = GroupsPivotes::with(['group', 'seller', 'document'])
                    ->where('group_id', $group_id)
                    ->orderBy('id', 'desc');

                $count_total1 = $data->count();

                return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('photo', function($row) {
                    return '<a href="' . $row->document->group_document . '" download class="btn btn-primary btn-sm">Download File</a>';
                })
                ->with([
                    "recordsTotal" => $count_total1,
                    "recordsFiltered" => $count_total1,
                ])
                ->rawColumns(['photo'])
                ->make(true);
            }
            return view('group.groupRequest.index',compact('group_id'));
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

}

