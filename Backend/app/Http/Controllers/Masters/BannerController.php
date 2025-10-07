<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Bannerslider;

class BannerController extends Controller
{
    public function index()
    {
        return view('masters.banners.index');
    }

    public function getBanners(Request $request)
    {
        if ($request->ajax()) {
            $query = Bannerslider::query();

            if ($request->filled('banner_id')) {
                $query->where('title', $request->input('banner_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('photo', function($row){
                    $imgurl = "";
                    return $imgurl = '<img src="'.$row->photo.'"/>';
                })
//                ->addColumn('status', function($row){
//                    if($row->status == 0){
//                        $status = '<label class="badge badge-danger">Inactive</label>';
//                    }else{
//                        $status = '<label class="badge badge-success">Active</label>';
//                    }
//                    return $status;
//                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('banners.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
//                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['photo','action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('masters.banners.add');
    }

    // public function store(Request $request)
    // {

    //     $request->validate([
    //         'title' => 'required',
    //         'description' => 'required',
    //         'photo' => 'required'
    //     ]);
    //     $imgpath = "";
    //     if ($request->hasFile('photo')) {
    //         $imageName = time().'.'.$request->photo->extension();
    //         $request->photo->storeAs('public/Banners', $imageName);
    //         $imgpath = url('/storage/Banners/'.$imageName);
    //     }



    //     Bannerslider::create([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'photo' => $imgpath,
    //         'status' => $request->status
    //     ]);

    //     return redirect()->route('banners.index')->with('success', 'banner created successfully.');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo' => 'required|array',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        foreach ($request->file('photo') as $item) {
            $imageName = time() . '_' . uniqid() . '.' . $item->getClientOriginalExtension();
            $item->storeAs('public/Banners', $imageName);
            $imgpath = url('/storage/Banners/' . $imageName);

            Bannerslider::create([
                'title' => $imageName, // Assuming the same title for all photos
                'description' => $imageName, // Assuming the same description for all photos
                'photo' => $imgpath,
                'status' => 1
            ]);
        }

        return redirect()->route('banners.index')->with('success', 'Banners created successfully.');
    }


    public function show(Bannerslider $Bannerslider)
    {
        return view('masters.banners.show', compact('Bannerslider'));
    }

    public function edit(Bannerslider $banner)
    {
        return view('masters.banners.edit', compact('banner'));
    }

    public function update(Request $request, Bannerslider $banner)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $imgpath = $banner->photo;
        if ($request->hasFile('photo')) {
            $imageName = time().'.'.$request->photo->extension();
            $request->photo->storeAs('public/Banners', $imageName);
            $imgpath = url('/storage/Banners/'.$imageName);
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $imgpath,
            'status' => 1
        ]);

        return redirect()->route('banners.index')->with('success', 'banners updated successfully.');
    }

    public function destroy(Bannerslider $banner)
    {
        try{
            $banner->delete();
            return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
        }catch(\Exception $e) {

        }
    }
}
