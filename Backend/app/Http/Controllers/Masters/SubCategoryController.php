<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('masters.subCategories.index');
    }

    public function getSubCategories(Request $request)
    {
        if ($request->ajax()) {
            $query = SubCategory::with('getCategory');
            if ($request->filled('language_id')) {
                $query->where('category_id', $request->input('language_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('photo', function($row) {
                    return '<a target="_blank" href="' . $row->photo . '" class="lightgallery-item"><img src="' . $row->photo . '" height="50px"></a>';
                })
                ->addColumn('category', function($row){
                    return $row->getCategory->name ? $row->getCategory->name : 'N/A'; // Handle null case
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('subCategories.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'category','photo'])
                ->make(true);
        }
    }



    public function create()
    {
        $category = Category::all();
        return view('masters.subCategories.add',compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'categories_id' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $subCategory = new SubCategory();
        $subCategory->name = $request->name;
        $subCategory->categories_id = $request->categories_id;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('subCatPhoto', 'public');
            $subCategory->photo = $path;
        }

        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'Sub Category created successfully']);
    }

    public function show(SubCategory $language)
    {
        return view('masters.subCategories.show', compact('language'));
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::all();
        return view('masters.subCategories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required',
            'categories_id' => 'required'
        ]);

        $subCategory = SubCategory::where('id',$id)->first();
        $subCategory->name=$request->name;
        $subCategory->categories_id=$request->categories_id;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('subCatPhoto', 'public');
            $subCategory->photo = $path;
        }
        $subCategory->save();

        return redirect()->route('subCategories.index')->with('success', 'Language updated successfully.');
    }

    public function destroy($id)
    {
        $category = SubCategory::findOrFail($id);
        $category->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }
}
