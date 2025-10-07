<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\SellerDetail;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index()
    {
        return view('masters.items.index');
    }

    public function getItems(Request $request)
    {
        if ($request->ajax()) {
            $query = Item::query();

            if ($request->filled('language_id')) {
                $query->where('name', $request->input('language_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('item_photo', function($row) {
                    return '<a target="_blank" href="' . $row->item_photo . '" class="lightgallery-item"><img src="' . $row->item_photo . '" alt="' . $row->item_name . '" height="50px"></a>';
                })
                ->addColumn('status', function($row){
                    if($row->item_status == 1){
                        $status = '<label class="badge badge-success">Active</label>';
                    }else{
                        $status = '<label class="badge badge-danger">Inactive</label>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('items.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action','item_photo','status'])
                ->make(true);
        }
    }

    public function create()
    {
        $category = Category::all();
        return view('masters.items.add',compact('category'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'item_name' => 'required',
                'item_description' => 'required',
                'item_weight' => 'required',
                'item_status' => 'required',
                'item_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->passes()) {
                if ($request->hasFile('item_photo')) {
                    $imageName = time().'.'.$request->item_photo->extension();
                    $request->item_photo->storeAs('public/Item', $imageName);
                }
                Item::create([
                    'item_name' => $request->item_name,
                    'item_description' => $request->item_description,
                    'item_weight' => $request->item_weight,
                    'item_status' => $request->item_status,
                    'item_photo' => isset($imageName) ? $imageName : null, // Save the file name if uploaded
                ]);

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

    public function show(Item $language)
    {
        return view('masters.items.show', compact('language'));
    }

    public function edit($id)
    {
        $data = Item::findOrFail($id);
        $allCategory = Category::all();
        $allSubCategory = SubCategory::where('categories_id',$data->category_id)->get();
        return view('masters.items.edit', compact('data','allCategory','allSubCategory'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'item_name' => 'required',
                'item_description' => 'required',
                'item_weight' => 'required',
                'item_status' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->passes()) {
                $item = Item::findOrFail($id);
                $item->item_name = $request->item_name;
                $item->item_description = $request->item_description;
                $item->item_weight = $request->item_weight;
                $item->item_status = $request->item_status;

                if ($request->hasFile('item_photo')) {
                    // Delete old image if exists
                    if ($item->item_photo) {
                        Storage::delete('public/Item/' . $item->item_photo);
                    }
                    $imageName = time().'.'.$request->item_photo->extension();
                    $request->item_photo->storeAs('public/Item', $imageName);
                    $item->item_photo = $imageName;
                }

                $item->save();

                DB::commit();

                return response()->json(['status' => true, 'message' => 'Record updated successfully.']);
            } else {
                return response()->json(['status' => false, 'message'=>$validator->errors()->all()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function destroy($id)
    {
        $category = Item::findOrFail($id);
        $category->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }
}
