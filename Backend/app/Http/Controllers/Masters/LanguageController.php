<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LanguageController extends Controller
{
    public function index()
    {
        return view('masters.languages.index');
    }

    public function getLanguages(Request $request)
    {
        if ($request->ajax()) {
            $query = Language::query();

            if ($request->filled('language_id')) {
                $query->where('name', $request->input('language_id'));
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('languages.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('masters.languages.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Language::create($request->all());

        return redirect()->route('languages.index')->with('success', 'Language created successfully.');
    }

    public function show(Language $language)
    {
        return view('masters.languages.show', compact('language'));
    }

    public function edit(Language $language)
    {
        return view('masters.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $language->update($request->all());

        return redirect()->route('languages.index')->with('success', 'Language updated successfully.');
    }

    public function destroy(language $language)
    {
        $language->delete();
        return response()->json(['status'=> true,'success' => 'Record deleted successfully']);
    }
}
