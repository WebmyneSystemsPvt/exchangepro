<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FAQController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index(Request $request)
    {
        try {
            $faqs = FAQ::orderBy('order', 'asc')->get();
            if ($request->ajax()) {
                return DataTables::of($faqs)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="'.url('/faqsEdit/'.$row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('pages.faqs.index',compact('faqs'));
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('pages.faqs.add');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'question.*' => 'required|string',
                'answer.*' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
            }

            $createdFAQs = [];

            // Process each FAQ entry
            foreach ($request->input('question') as $key => $question) {
                $faq = FAQ::create([
                    'question' => $question,
                    'answer' => $request->input('answer')[$key],
                ]);
                $faq->update(['order' => $faq->id]);
                $createdFAQs[] = $faq;
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'FAQs created successfully.', 'faqs' => $createdFAQs]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getEdit($id)
    {
        try {
            $faq = FAQ::where('id',$id)->first();
            return view('pages.faqs.edit', compact('faq'));
        }catch(\Exception $ex){
            dd($ex);
        }
    }


    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation rules
            $rules = [
                'question' => 'required|string',
                'answer' => 'required|string',
            ];

            // Validate request
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
            }

            // Update FAQ record
            $faq = FAQ::findOrFail($request->input('editId'));
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Record updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $faq = FAQ::findOrFail($id);
            $faq->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'FAQ deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    public function destroy(FAQ $testimonial)
    {
        $testimonial->delete();

        return response()->json(['status' => true, 'message' => 'FAQ deleted successfully.']);
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'faqIds' => 'required|array'
        ]);
        try {
            $faqIds = $request->faqIds;
            $faqUpdates = [];
            foreach ($faqIds as $index => $id) {
                $faq = FAQ::find($id);
                if ($faq) {
                    $faq->order = $index + 1;
                    $faqUpdates[] = $faq;
                }
            }
            foreach ($faqUpdates as $faq) {
                $faq->save();
            }
            return response()->json(['status' => true, 'message' => 'FAQ order updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }




    public function load()
    {
        try {
            $faqs = FAQ::orderBy('order')->get();
            return response()->json($faqs);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}

