<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('settings.index');
    }

    public function getSettings(Request $request)
    {
        if ($request->ajax()) {
            $query = Setting::query();

            if ($request->filled('language_id')) {
                $query->where('name', $request->input('language_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('settings.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
//                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create(){
        $roles = Setting::all();
        return view('settings.add',compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'margin_percentage' => 'required',
                'application_fee' => 'required',
                'others_fee' => 'required',
                'tax' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->passes()) {
                $new_user_name = $request->input('margin_percentage');
                $application_fee = $request->input('application_fee');
                $others_fee = $request->input('others_fee');
                $tax = $request->input('tax');

                Setting::create([
                    'margin_percentage'=>$new_user_name,
                    'application_fee'=>$application_fee,
                    'others_fee'=>$others_fee,
                    'tax'=>$tax
                ]);

                DB::commit();
                return redirect('/settings');
                //return response()->json(['status' => true, 'message' => 'Record saved successfully.']);
            }else{
                return response()->json(['status' => false, 'message'=>$validator->errors()->all()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show(Request $request, $user_id) {
        $target_user = Setting::find($user_id);

        return view('settings.show', [
            'user' => $target_user
        ]);
    }

    public function edit(Request $request, $id) {
        $data = Setting::find($id);
        return view('settings.edit',compact('data'));
    }

    public function update(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'margin_percentage' => 'required',
                'application_fee' => 'required',
                'others_fee' => 'required',
                'tax' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->passes()) {
                $user = Setting::find($id);
                $user->margin_percentage = $request->input('margin_percentage');
                $user->application_fee = $request->input('application_fee');
                $user->others_fee = $request->input('others_fee');
                $user->tax = $request->input('tax');
                $user->save();
                DB::commit();
                return redirect('/settings');
            } else {
                return response()->json(['status' => false, 'errors' => $validator->errors()->all()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }

    }

    public function delete($id)
    {
        Setting::find($id)->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }
}
