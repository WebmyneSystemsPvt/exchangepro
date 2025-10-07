<?php
namespace App\Http\Controllers;

use App\Models\BorrowerDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class BorrowersController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }


    public function index()
    {
        return view('users.borrowers.index');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $borrowerRole = Role::where('name', 'borrower')->first();
            $data = User::role($borrowerRole)->with(['borrowerDetails'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    if($row->status == 1){
                        $status = '<label class="badge badge-success">Active.</label>.';
                    }else{
                        $status = '<label class="badge badge-danger">Inactive</label>';
                    }
                    return $status;
                })
                ->addColumn('pincode', function($row){
                    return $row->borrowerDetails->pincode ?? '-';
                })
                ->addColumn('phone_number', function($row){
                    return $row->borrowerDetails->phone_number ?? '-';
                })

                ->addColumn('action', function($row){
                    $btn = '<a href="/users/'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action','status','pincode','phone_number'])
                ->make(true);
        }
    }

    public function create(){
        $roles = Role::all();
        return view('users.borrowers.add',compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required',
                'password' => [
                    'required',
                    'min:6',
                    'required_with:password_confirmation',
                    'same:password_confirmation',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                ],
                'password_confirmation' => 'min:6',
                'email' => 'required|email|unique:users,email',
                'city' => 'required',
                'pincode' => 'required',
                'location' => 'required',
                'phone_number' => 'required',
                'address' => 'required'
            ];

            $messages = [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 6 characters long.',
                'password.required_with' => 'The password confirmation is required when password is present.',
                'password.same' => 'The password and password confirmation must match.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
                // Add other custom messages for other fields if needed
            ];

            $validator = Validator::make($request->all(), $rules,$messages);
            if ($validator->passes()) {
                $new_user_name = $request->input('name');
                $new_user_email = $request->input('email');
                $new_user_role = config('constants.BORROWER');
                $pass = Hash::make($request->input('password'));
                $new_user_status = $request->input('status');

                $new_user = User::create([
                    'name'=>$new_user_name,
                    'email' => $new_user_email,
                    'password' => $pass,
                    'status' => $new_user_status
                ]);

                if (isset($new_user_role)) {
                    $user = User::find($new_user->id);
                    $user->assignRole($new_user_role);

                }
                if($new_user){
                    $BorrowerDetail = new BorrowerDetail();
                    $BorrowerDetail->user_id = $new_user->id;
                    $BorrowerDetail->city = $request->input('city');
                    $BorrowerDetail->address = $request->input('address');
                    $BorrowerDetail->pincode = $request->input('pincode');
                    $BorrowerDetail->location = $request->input('location');
                    $BorrowerDetail->phone_number = $request->input('phone_number');
                    $BorrowerDetail->save();
                }

                DB::commit();

                return response()->json(['status' => true, 'message' => 'Record saved successfully.']);
            }else{
                return response()->json(['status' => false, 'message'=>$validator->errors()->all()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show(Request $request, $user_id) {
        $target_user = User::find($user_id);

        return view('users.borrowers.show', [
            'user' => $target_user
        ]);
    }

    public function getEdit(Request $request, $id) {
        $user = User::with('roles','borrowerDetails')->find($id);
        $roles = Role::all();
        $userRole = $user->getRoleNames()[0];
        return view('users.borrowers.edit',compact('user','roles','userRole'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required',
                'password' => [
                    'nullable',
                    'min:6',
                    'required_with:password_confirmation',
                    'same:password_confirmation',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                ],
                'password_confirmation' => 'nullable|min:6',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'city' => 'required',
                'pincode' => 'required',
                'location' => 'required',
                'phone_number' => 'required',
                'address' => 'required'
            ];

            $messages = [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 6 characters long.',
                'password.required_with' => 'The password confirmation is required when password is present.',
                'password.same' => 'The password and password confirmation must match.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
                // Add other custom messages for other fields if needed
            ];
            $validator = Validator::make($request->all(), $rules,$messages);

            if ($validator->passes()) {
                $user = User::find($request->user_id);
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->status = $request->input('status');
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
                $user->save();
                if($user){
                    $BorrowerDetail = BorrowerDetail::where('user_id',$request->user_id)->first();
                    $BorrowerDetail->city = $request->input('city');
                    $BorrowerDetail->address = $request->input('address');
                    $BorrowerDetail->pincode = $request->input('pincode');
                    $BorrowerDetail->location = $request->input('location');
                    $BorrowerDetail->phone_number = $request->input('phone_number');
                    $BorrowerDetail->save();
                }
                DB::commit();

                return response()->json(['status' => true, 'message' => 'Record updated successfully.']);
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
        User::find($id)->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }


}
