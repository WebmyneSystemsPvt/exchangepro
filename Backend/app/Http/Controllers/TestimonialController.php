<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Testimonial::get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="'.url('/testimonials/'.$row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('pages.testimonials.index');
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('pages.testimonials.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Custom validation messages
            $messages = [
                'testimonials.*.user_name.required' => 'User name is required.',
                'testimonials.*.user_position.required' => 'User position is required.',
                'testimonials.*.testimonial.required' => 'Testimonial is required.',
                'testimonials.*.photo.image' => 'The file must be an image.',
                'testimonials.*.photo.max' => 'The image may not be greater than 2MB in size.',
            ];

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'testimonials.*.user_name' => 'required|string|max:255',
                'testimonials.*.user_position' => 'required|string|max:255',
                'testimonials.*.user_company' => 'nullable|string|max:255',
                'testimonials.*.testimonial' => 'required|string',
                'testimonials.*.photo' => 'nullable|image|max:2048'
            ], $messages);

            // If validation fails, return error messages
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
            }

            // Process each testimonial data
            foreach ($request->testimonials as $testimonialData) {
                // Handle file upload if photo is present
                if (isset($testimonialData['photo'])) {
                    $photoPath = $testimonialData['photo']->store('testimonial_photos', 'public');
                    $testimonialData['photo'] = $photoPath;
                }

                // Create testimonial entry
                Testimonial::create($testimonialData);
            }

            // Commit transaction if all operations succeed
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Testimonials created successfully.']);
        } catch (\Exception $e) {
            // Rollback transaction and return error message on exception
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit(Testimonial $testimonial)
    {
        return view('pages.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        try {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'user_position' => 'required|string|max:255',
                'user_company' => 'nullable|string|max:255',
                'testimonial' => 'required|string',
                'photo' => 'nullable|image|max:2048' // Assuming max size for photo is 2MB
            ]);

            $testimonialData = $request->only('user_name', 'user_position', 'user_company', 'testimonial');

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('testimonial_photos', 'public');
                $testimonialData['photo'] = $photoPath;
            }

            $testimonial->update($testimonialData);

            return response()->json([
                'status' => true,
                'message' => 'Testimonial updated successfully.'
            ]);
        }catch (\Exception $ex){
            return response()->json([
                'status' => false,
                'message' => 'Failed to update testimonial.'
            ], 500); // Use appropriate HTTP status code

        }

    }

    public function destroy(Testimonial $testimonial)
    {
        try {
            $testimonial->delete();
            return response()->json(['status' => true, 'message' => 'Testimonial deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete the testimonial.']);
        }
    }


}
