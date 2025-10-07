<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GenderController extends Controller
{
    public function index()
    {
        return view('masters.genders.index');
    }

    public function getGenders(Request $request)
    {
        if ($request->ajax()) {
            $query = Gender::query();

            if ($request->filled('gender_id')) {
                $query->where('name', $request->input('gender_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('genders.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('masters.genders.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Gender::create($request->all());

        return redirect()->route('genders.index')->with('success', 'genders created successfully.');
    }

    public function show(Gender $gender)
    {
        return view('masters.genders.show', compact('gender'));
    }

    public function edit(Gender $gender)
    {
        return view('masters.genders.edit', compact('gender'));
    }

    public function update(Request $request, Gender $gender)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $gender->update($request->all());

        return redirect()->route('genders.index')->with('success', 'genders updated successfully.');
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }
}
