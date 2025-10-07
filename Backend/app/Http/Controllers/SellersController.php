<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStorage;
use App\Models\ItemStorageBlockDays;
use App\Models\ItemStorageFacilityOffer;
use App\Models\ItemStoragePhoto;
use App\Models\ItemStoragePivot;
use App\Models\ItemStorageRating;
use App\Models\ItemStorageTag;
use App\Models\ItemStorageTermsCondition;
use App\Models\SellerDetail;
use App\Models\SubCategory;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;



class SellersController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }


    public function index()
    {
        return view('users.sellers.index');
    }

    public function getUsers(Request $request)
    {

        if ($request->ajax()) {
            $sellerRole = Role::where('name', 'seller')->firstOrFail(); // Ensure role exists or throw an exception
            $data = User::role($sellerRole)
                ->with(['sellerDetails', 'itemStorage'])
                ->withCount('itemStorage')
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '<a href="/sellerusers/'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteBtn btn btn-danger btn-sm" data-id="'.$row->id.'">Delete</a>';
                    return $btn;
                })
                ->addColumn('company_name', function($row) {
                    return $row->sellerDetails->company_name ?? '-';
                })
                ->addColumn('pincode', function($row) {
                    return $row->sellerDetails->pincode ?? '-';
                })
                ->addColumn('phone_number', function($row) {
                    return $row->sellerDetails->phone_number ?? '-';
                })
                ->addColumn('itemStorageCount', function($row) {
                    $badge = '<span class="badge badge-light" title="'.$row->item_storage_count.' count click here to go to list.">';
                    $badge .= '<a target="_blank" href="'.url('/seller-item-storage-list/'.$row->id).'"><i class="menu-icon mdi mdi-link-variant"></i>&nbsp;&nbsp;'.$row->item_storage_count.'</a>';
                    $badge .= '</span>';
                    return $badge;
                })
                ->addColumn('status', function($row) {
                    $status = $row->status === 1 ? '<label class="badge badge-success">Active</label>' : '<label class="badge badge-danger">Inactive</label>';
                    return $status;
                })
                ->rawColumns(['action', 'status', 'company_name', 'pincode', 'phone_number','itemStorageCount'])
                ->make(true);
        }
    }


    public function create(){
        $roles = Role::all();
        return view('users.sellers.add',compact('roles'));
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
                'company_name' => 'required',
                'city' => 'required',
                'pincode' => 'required',
                'location' => 'required',
                'phone_number' => 'required',
                'availabilityMF' => 'required',
                'availabilitySS' => 'required',
                'address' => 'required'
            ];

            $messages = [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 6 characters long.',
                'password.required_with' => 'The password confirmation is required when password is present.',
                'password.same' => 'The password and password confirmation must match.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
                'availabilityMF.required' => 'The Mon to Fri Availability time is required.',
                'availabilitySS.required' => 'The Sat to Sun Availability time is required.'
            ];

            $validator = Validator::make($request->all(), $rules,$messages);
            if ($validator->passes()) {
                $new_user_name = $request->input('name');
                $new_user_email = $request->input('email');
                $new_user_role = config('constants.SELLER');
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
                    $SellerDetail = new SellerDetail();
                    $SellerDetail->user_id = $new_user->id;
                    $SellerDetail->company_name = $request->input('company_name');
                    $SellerDetail->city = $request->input('city');
                    $SellerDetail->address = $request->input('address');
                    $SellerDetail->pincode = $request->input('pincode');
                    $SellerDetail->location = $request->input('location');
                    $SellerDetail->phone_number = $request->input('phone_number');
                    $SellerDetail->availabilityMF = $request->input('availabilityMF');
                    $SellerDetail->availabilitySS = $request->input('availabilitySS');
                    $SellerDetail->save();
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

        return view('users.sellers.show', [
            'user' => $target_user
        ]);
    }

    public function getEdit(Request $request, $id) {
        $user = User::with('roles','sellerDetails')->find($id);
        $roles = Role::all();
        $userRole = $user->getRoleNames()[0];
        return view('users.sellers.edit',compact('user','roles','userRole'));
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
                'company_name' => 'required',
                'city' => 'required',
                'pincode' => 'required',
                'location' => 'required',
                'phone_number' => 'required',
                'availabilityMF' => 'required',
                'availabilitySS' => 'required',
                'address' => 'required'
            ];

            $messages = [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 6 characters long.',
                'password.required_with' => 'The password confirmation is required when password is present.',
                'password.same' => 'The password and password confirmation must match.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
                'availabilityMF.required' => 'The Mon to Fri Availability time is required.',
                'availabilitySS.required' => 'The Sat to Sun Availability time is required.'
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
                    $SellerDetail = SellerDetail::where('user_id',$request->user_id)->first();
                    $SellerDetail->company_name = $request->input('company_name');
                    $SellerDetail->city = $request->input('city');
                    $SellerDetail->address = $request->input('address');
                    $SellerDetail->pincode = $request->input('pincode');
                    $SellerDetail->location = $request->input('location');
                    $SellerDetail->phone_number = $request->input('phone_number');
                    $SellerDetail->availabilityMF = $request->input('availabilityMF');
                    $SellerDetail->availabilitySS = $request->input('availabilitySS');
                    $SellerDetail->save();
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

    public function updateStatus(Request $request)
    {
        try {
            $user = ItemStorage::where('id',$request->user_id)->first();
            $user->status = $request->status;
            $user->save();

            return response()->json(['status' => true, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function sellerItemStorageDetails(Request $request,$id){
        try {
            $data = ItemStorage::where('id',$id)
                ->with(['category',
                    'subCategory',
                    'items',
                    'photos',
                    'sellers',
                    'facilityOffers',
                    'termsConditions',
                    'tags',
                    'ratings' => function($query) {
                        $query->select('id', 'seller_id','item_storage_id', 'rating', 'description','status','created_at')->with('seller:id,name,avatar');
                    },
                    'itemStorageBlockDays'
                ])
                ->first();
//            dd($data);
            return view('users.sellers.itemStorage.show', compact('data'));
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function pendingItemStorageList(Request $request){
        try {
            if ($request->ajax()) {
                $data = ItemStorage::with('category', 'subCategory', 'items', 'photos', 'sellers')
                    ->where('status', 0)
                    ->orderBy('id', 'desc');
                $count_total1 = $data->count();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->with([
                        "recordsTotal" => $count_total1,
                        "recordsFiltered" => $count_total1,
                    ])
                    ->addColumn('seller', function ($row) {
                        return $row->sellers->name ?? 'N/A';
                    })
                    ->addColumn('itemName', function ($row) {
                        return $row->items->pluck('item_name')->implode(', ') ?? 'N/A';
                    })
                    ->rawColumns(['itemName','seller'])
                    ->make(true);
            }
            return view('pendingList.index');
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function addItemStorage(){
        try {
            $sellers = User::where('status', 1)
                ->whereHas('roles', function($query) {
                    $query->where('name', config('constants.SELLER'));
                })
            ->get();

            $items = Item::where('item_status','=','1')->get();

            $category = Category::get();

//            $subCategory = SubCategory::get();

            return view('users.sellers.itemStorage.add',compact('sellers','items','category'));
        }catch (\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function storeItemStorage(Request $request)
    {
        $defaultMessages = [
            'listing_type.required' => 'The Listing Type field is required.',
            'user_id.required' => 'The seller field is required.',
            'categories_id.required' => 'The category field is required.',
            'categories_id.exists' => 'The selected category is invalid.',
            'sub_categories_id.required' => 'The subcategory field is required.',
            'sub_categories_id.exists' => 'The selected subcategory is invalid.',
            'location.required' => 'The location field is required.',
            'description.required' => 'The Description field is required.',
            'exception_details.required' => 'The Exception Details field is required.',
            'rate.required' => 'The Rate field is required.',
            'rented_max_allow_days.required' => 'The Rented Max Allow Days field is required.',
            'blocked_days.required' => 'The Blocked Days field is required.',
            'terms_conditions.*.title.required_with' => 'The title field is required when terms and conditions are present.',
            'terms_conditions.*.description.required_with' => 'The description field is required when terms and conditions are present.',
            'facility_offers.*.title.required_with' => 'The title field is required when facility offers are present.',
            'facility_offers.*.photo.required_with' => 'The photo field is required when facility offers are present.',
            'facility_offers.*.photo.image' => 'The photo must be an image.',
            'facility_offers.*.description.required_with' => 'The description field is required when facility offers are present.',
            'storage_photos.*.image' => 'Each storage photo must be an image.',
        ];

        $additionalMessages = [];
        if ($request->categories_id == 1) {
            $additionalMessages = ['item_id.*.required' => 'Item is required'];
        }

        $messages = array_merge($defaultMessages, $additionalMessages);

        $validator = Validator::make($request->all(), [
            'listing_type' => 'nullable|string|max:255',
            'user_id' => 'required',
            'categories_id' => 'required|exists:categories,id',
            'sub_categories_id' => 'required|exists:sub_categories,id',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exception_details' => 'nullable|string',
            'rate' => 'nullable|string|max:255',
            'rented_max_allow_days' => 'nullable|integer',
            'blocked_days' => 'nullable|string',
            'terms_conditions' => 'nullable|array',
            'terms_conditions.*.title' => 'required_with:terms_conditions|string|max:255',
            'terms_conditions.*.description' => 'required_with:terms_conditions|string',
            'facility_offers' => 'nullable|array',
            'facility_offers.*.title' => 'required_with:facility_offers|string|max:255',
            'facility_offers.*.photo' => 'required_with:facility_offers|image',
            'facility_offers.*.description' => 'required_with:facility_offers|string',
            'item_id' => $request->categories_id == 1 ? ['required', 'array'] : 'nullable|array',
            'default_storage_photo' => 'nullable',
            'storage_photos' => 'nullable|array',
            'storage_photos.*' => 'nullable|image',
            'country'=>'nullable|string',
            'state'=>'nullable|string',
            'city'=>'nullable|string',
            'pincode'=>'nullable|string',
            'landmark'=>'nullable|string',
            'latitude'=>'nullable|string',
            'longitude'=>'nullable|string',
            'tags' => 'nullable|string'
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();
            $itemStorage = new ItemStorage();
            $itemStorage->fill($validated);
            $itemStorage->save();

            if ($request->has('item_id') && count($request->item_id) > 0) {
                foreach ($request->item_id as $itemId) {
                    $itemStoragePivot = new ItemStoragePivot();
                    $itemStoragePivot->item_storage_id = $itemStorage->id;
                    $itemStoragePivot->item_id = trim($itemId);
                    $itemStoragePivot->save();
                }
            }

            if ($request->hasFile('storage_photos')) {
                $basePath = storage_path('app/public/item_storage');
                $thumbnailBasePath = $basePath . '/thumbnail';

                if (!File::exists($thumbnailBasePath)) {
                    File::makeDirectory($thumbnailBasePath, 0755, true);
                }

                foreach ($request->file('storage_photos') as $photo) {
                    // Store the original photo
                    $path = $photo->store('item_storage', 'public');

                    // Get the file modification time and format it
                    $fileModificationTime = filemtime($photo->getPathname());
                    $formattedDate = date('m/d/Y H:i:s A', $fileModificationTime);

                    // Create and modify the image
                    $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($photo);
                    // Add text in the top-right corner
                    $image->text($formattedDate, $image->width() - 1100, 750, function($font) {
                        $font->file(public_path('assets/fonts/Arial.ttf'));
                        $font->size(30);
                        $font->color('#ffffff');
                        $font->align('left');
                        $font->valign('bottom');
                    });

                    // Save the modified image
                    $image->save(storage_path('app/public/' . $path));

                    // Create and save the thumbnail
                    $thumbnailPath = 'item_storage/thumbnail/' . basename($path);
                    $thumbnailImage = $image->resize(1200, 800);
                    $thumbnailImage->save(storage_path('app/public/' . $thumbnailPath));

                    // Store photo details in the database
                    $itemStoragePhoto = new ItemStoragePhoto();
                    $itemStoragePhoto->item_photo = $thumbnailPath;
                    $itemStoragePhoto->item_storage_id = $itemStorage->id;
                    $itemStoragePhoto->save();
                }
            }

            if ($request->hasFile('default_storage_photo')) {
                $basePath = storage_path('app/public/default_storage_photo');
                $thumbnailPath = $basePath . '/thumbnail';
                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                $path2 = $request->file('default_storage_photo')->store('default_storage_photo', 'public');
                $thumbnailPath = 'default_storage_photo/thumbnail/' . basename($path2);
                $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($request->file('default_storage_photo'))->resize(1200, 800);
                $image->save(storage_path('app/public/' . $thumbnailPath));

                $itemStoragePhoto = ItemStorage::where('id',$itemStorage->id)->first();
                $itemStoragePhoto->default_storage_photo = $thumbnailPath;
                $itemStoragePhoto->save();

            }


            if (!empty($validated['terms_conditions'])) {
                foreach ($validated['terms_conditions'] as $term) {
                    $itemStorageTermCondition = new ItemStorageTermsCondition();
                    $itemStorageTermCondition->item_storage_id = $itemStorage->id;
                    $itemStorageTermCondition->title = $term['title'];
                    $itemStorageTermCondition->description = $term['description'];
                    $itemStorageTermCondition->save();
                }
            }


            if (!empty($validated['facility_offers'])) {
                $basePath = storage_path('app/public/item_storage_offers');
                $thumbnailPath = $basePath . '/thumbnail';

                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                foreach ($validated['facility_offers'] as $offer) {
                    $path = $offer['photo']->store('item_storage_offers', 'public');

                    $thumbnailPath1 = 'item_storage_offers/thumbnail/' . basename($path);
                    $image2 = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($offer['photo'])->resize(100, 100);
                    $image2->save(storage_path('app/public/' . $thumbnailPath1));

                    $itemStorageFacilityOffer = new ItemStorageFacilityOffer();
                    $itemStorageFacilityOffer->item_storage_id = $itemStorage->id;
                    $itemStorageFacilityOffer->title = $offer['title'];
                    $itemStorageFacilityOffer->photo = $path;
                    $itemStorageFacilityOffer->description = $offer['description'];
                    $itemStorageFacilityOffer->save();
                }
            }

            if (!empty($validated['tags'])) {
                $tagsArray = explode(',', $validated['tags']);
                foreach ($tagsArray as $tag) {
                    $itemStorageTag = new ItemStorageTag();
                    $itemStorageTag->item_storage_id = $itemStorage->id;
                    $itemStorageTag->tag_name = trim($tag);
                    $itemStorageTag->save();
                }
            }

            if (!empty($validated['blocked_days'])) {
                $tagsArray = explode(',', $validated['blocked_days']);
                foreach ($tagsArray as $blockdays) {
                    $blockdays = trim($blockdays);
                    $dateObject = DateTime::createFromFormat('m/d/Y', $blockdays);
                    if ($dateObject && $dateObject->format('m/d/Y') === $blockdays) {
                        $formattedDate = $dateObject->format('Y-m-d');

                        $itemStorageTag = new ItemStorageBlockDays();
                        $itemStorageTag->block_days_date = $formattedDate;
                        $itemStorageTag->item_storage_id = $itemStorage->id;
                        $itemStorageTag->save();
                    }
                }
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Item Storage Created Successfully.', 'seller_id' => $request->user_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function sellerItemStorageList(Request $request, $user_id)
    {
        try {

            if ($request->ajax()) {
                $dataQuery = ItemStorage::with('category', 'subCategory', 'items', 'photos', 'sellers')
                    ->withCount('ratings')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'desc');

                $count_total1 = $dataQuery->count();

                return Datatables::of($dataQuery)
                    ->addIndexColumn()
                    ->with([
                        "recordsTotal" => $count_total1,
                        "recordsFiltered" => $count_total1,
                    ])
                    ->addColumn('seller', function ($row) {
                        return $row->sellers->name ?? 'N/A';
                    })
                    ->addColumn('itemName', function ($row) {
                        return $row->items->pluck('item_name')->implode(', ') ?? 'N/A';
                    })
                    ->addColumn('ratingsCount', function($row) {
                        $badge = '<span class="badge badge-light" title="'.$row->ratings_count.' count click here to go to list.">';
                        $badge .= '<a target="_blank" href="'.url('/view-review-item-storage/'.$row->id).'"><i class="menu-icon mdi mdi-link-variant"></i>&nbsp;&nbsp;'.$row->ratings_count.'</a>';
                        $badge .= '</span>';
                        return $badge;
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = url('/edit-item-storage/' . $row->id);
                        $viewUrl = url('/seller-item-storage-details/' . $row->id);
                        return '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a> ' .
                            ' <a href="' . $viewUrl . '" class="edit btn btn-primary btn-sm">View</a>';
                    })
                    ->rawColumns(['action', 'itemName', 'seller','ratingsCount'])
                    ->make(true);
            }

            $userName = User::where('id', $user_id)->value('name');

            return view('users.sellers.itemStorage.index', compact('user_id', 'userName'));
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function editItemStorage(Request $request,$id){
        $data = ItemStorage::with([
            'sellers.sellerDetails',
            'category',
            'subCategory',
            'items',
            'photos',
            'facilityOffers',
            'termsConditions',
            'tags',
            'ratings',
            'itemStorageBlockDays'
        ])
        ->where('id', '=', $id)
        ->first();

        $itemsArray = $data->items->pluck('id')->toArray();

        //Block days dates convert to string in particular format
        $blockDaysArray = $data->itemStorageBlockDays->pluck('block_days_date')->toArray();
        $blockDaysArray = array_map(function($date) {
            $dateTime = DateTime::createFromFormat('d-m-Y', $date);
            return $dateTime ? $dateTime->format('m/d/Y') : $date;
        }, $blockDaysArray);
        $blockDaysArray = implode(', ', $blockDaysArray);

        //tags
        $tags = $data->tags->pluck('tag_name')->toArray();
        $tags = implode(', ', $tags);

        $sellers = User::where('status', 1)
        ->whereHas('roles', function($query) {
            $query->where('name', config('constants.SELLER'));
        })->get();

        $items = Item::where('item_status','=','1')->get();

        $category = Category::get();

        $subcategory = SubCategory::where('categories_id',$data->categories_id)->get();

        return view('users.sellers.itemStorage.edit', compact('data','sellers','items','category','subcategory','itemsArray','blockDaysArray','tags'));
    }

    public function UpdateItemStorage(Request $request)
    {
//        dd($request->all());
        $defaultMessages = [
            'listing_type.required' => 'The Listing Type field is required.',
            'user_id.required' => 'The seller field is required.',
            'categories_id.required' => 'The category field is required.',
            'categories_id.exists' => 'The selected category is invalid.',
            'sub_categories_id.required' => 'The subcategory field is required.',
            'sub_categories_id.exists' => 'The selected subcategory is invalid.',
            'location.required' => 'The location field is required.',
            'description.required' => 'The Description field is required.',
            'exception_details.required' => 'The Exception Details field is required.',
            'rate.required' => 'The Rate field is required.',
            'rented_max_allow_days.required' => 'The Rented Max Allow Days field is required.',
            'blocked_days.required' => 'The Blocked Days field is required.',
            'terms_conditions.*.title.required_with' => 'The title field is required when terms and conditions are present.',
            'terms_conditions.*.description.required_with' => 'The description field is required when terms and conditions are present.',
        ];

        $additionalMessages = [];
        if ($request->categories_id == 1) {
            $additionalMessages = ['item_id.*.required' => 'Item is required'];
        }

        $messages = array_merge($defaultMessages, $additionalMessages);

        $validator = Validator::make($request->all(), [
            'listing_type' => 'nullable|string|max:255',
            'user_id' => 'required',
            'categories_id' => 'required|exists:categories,id',
            'sub_categories_id' => 'required|exists:sub_categories,id',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exception_details' => 'nullable|string',
            'rate' => 'nullable|string|max:255',
            'rented_max_allow_days' => 'nullable|integer',
            'blocked_days' => 'nullable|string',
            'terms_conditions' => 'nullable|array',
            'terms_conditions.*.title' => 'required_with:terms_conditions|string|max:255',
            'terms_conditions.*.description' => 'required_with:terms_conditions|string',
            'item_id' => $request->categories_id == 1 ? ['required', 'array'] : 'nullable|array',
            'default_storage_photo' => 'nullable|image',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'pincode' => 'nullable|string',
            'landmark' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'tags' => 'nullable|string',
            'facility_offers' => 'nullable|array',
            'facility_offers.*.title' => 'required_with:facility_offers|string|max:255',
            'facility_offers.*.description' => 'required_with:facility_offers|string',
            'facility_offers.*.photo' => 'nullable|image',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();
            $itemStorage = ItemStorage::findOrFail($request->editId);
            $itemStorage->fill($validated);
            $itemStorage->save();

            if ($request->has('item_id') && count($request->item_id) > 0) {
                ItemStoragePivot::where('item_storage_id', $itemStorage->id)->delete();
                foreach ($request->item_id as $itemId) {
                    $itemStoragePivot = new ItemStoragePivot();
                    $itemStoragePivot->item_storage_id = $itemStorage->id;
                    $itemStoragePivot->item_id = trim($itemId);
                    $itemStoragePivot->save();
                }
            }

            if ($request->hasFile('storage_photos')) {
                $basePath = storage_path('app/public/item_storage');
                $thumbnailPath = $basePath . '/thumbnail';

                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                foreach ($request->file('storage_photos') as $photo) {
                    $path = $photo->store('item_storage', 'public');
                    $thumbnailPath = 'item_storage/thumbnail/' . basename($path);
                    $fileModificationTime = filemtime($photo->getPathname());
                    $formattedDate = date('m/d/Y H:i:s A', $fileModificationTime);
                    $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($photo)->resize(1200, 800);
                    // Add text in the top-right corner
                    $image->text($formattedDate, $image->width() - 1100, 750, function($font) {
                        $font->file(public_path('assets/fonts/Arial.ttf'));
                        $font->size(30);
                        $font->color('#ffffff');
                        $font->align('left');
                        $font->valign('bottom');
                    });
                    $image->save(storage_path('app/public/' . $thumbnailPath));
                    $itemStoragePhoto = new ItemStoragePhoto();
                    $itemStoragePhoto->item_photo = $thumbnailPath;
                    $itemStoragePhoto->item_storage_id = $itemStorage->id;
                    $itemStoragePhoto->save();
                }
            }

            if ($request->hasFile('default_storage_photo')) {
                $basePath = storage_path('app/public/default_storage_photo');
                $thumbnailPath = $basePath . '/thumbnail';
                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                $path2 = $request->file('default_storage_photo')->store('default_storage_photo', 'public');
                $thumbnailPath = 'default_storage_photo/thumbnail/' . basename($path2);
                $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($request->file('default_storage_photo'))->resize(1200, 800);
                $image->save(storage_path('app/public/' . $thumbnailPath));

                $itemStoragePhoto = ItemStorage::where('id',$itemStorage->id)->first();
                $itemStoragePhoto->default_storage_photo = $thumbnailPath;
                $itemStoragePhoto->save();
            }

            if (!empty($validated['terms_conditions'])) {
                ItemStorageTermsCondition::where('item_storage_id', $itemStorage->id)->delete();
                foreach ($validated['terms_conditions'] as $term) {
                    $itemStorageTermCondition = new ItemStorageTermsCondition();
                    $itemStorageTermCondition->item_storage_id = $itemStorage->id;
                    $itemStorageTermCondition->title = $term['title'];
                    $itemStorageTermCondition->description = $term['description'];
                    $itemStorageTermCondition->save();
                }
            }

            if (!empty($validated['facility_offers'])) {
                // Delete existing facility offers for this item storage
                ItemStorageFacilityOffer::where('item_storage_id', $itemStorage->id)->delete();

                $basePath = storage_path('app/public/item_storage_offers');
                $thumbnailPath = $basePath . '/thumbnail';

                // Create directory if it doesn't exist
                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                foreach ($validated['facility_offers'] as $offer) {
                    $path = null;

                    // Check if 'photo' key exists and is a valid instance of UploadedFile
                    if (isset($offer['photo']) && $offer['photo'] instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            // Store the file
                            $path = $offer['photo']->store('item_storage_offers', 'public');

                            // Create and save the thumbnail
                            $thumbnailPath1 = 'item_storage_offers/thumbnail/' . basename($path);
                            $image2 = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($offer['photo'])->resize(100, 100);
                            $image2->save(storage_path('app/public/' . $thumbnailPath1));
                        } catch (\Exception $e) {
                            // Log error or handle file processing issues
                            Log::error('Error processing file for facility offer: ' . $e->getMessage());
                            continue; // Skip this offer and proceed with others
                        }
                    } else {
                        // If no file is present, ensure $path is null
                        $path = null;
                    }

                    // Create or update the ItemStorageFacilityOffer record
                    $itemStorageFacilityOffer = ItemStorageFacilityOffer::updateOrCreate(
                        ['item_storage_id' => $itemStorage->id, 'title' => $offer['title']], // Unique identifier for update
                        [
                            'title' => $offer['title'] ?? null,
                            'photo' => $path,
                            'description' => $offer['description'] ?? null
                        ]
                    );
                }
            }


            if (!empty($validated['tags'])) {
                ItemStorageTag::where('item_storage_id', $itemStorage->id)->delete();
                $tagsArray = explode(',', $validated['tags']);
                foreach ($tagsArray as $tag) {
                    $itemStorageTag = new ItemStorageTag();
                    $itemStorageTag->item_storage_id = $itemStorage->id;
                    $itemStorageTag->tag_name = trim($tag);
                    $itemStorageTag->save();
                }
            }

            if (!empty($validated['blocked_days'])) {
                ItemStorageBlockDays::where('item_storage_id', $itemStorage->id)->delete();
                $tagsArray = explode(',', $validated['blocked_days']);
                foreach ($tagsArray as $blockdays) {
                    $blockdays = trim($blockdays);
                    $dateObject = DateTime::createFromFormat('m/d/Y', $blockdays);
                    if ($dateObject && $dateObject->format('m/d/Y') === $blockdays) {
                        $formattedDate = $dateObject->format('Y-m-d');

                        $itemStorageTag = new ItemStorageBlockDays();
                        $itemStorageTag->block_days_date = $formattedDate;
                        $itemStorageTag->item_storage_id = $itemStorage->id;
                        $itemStorageTag->save();
                    }
                }
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Item Storage updated Successfully.', 'seller_id' => $request->user_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function deletePhoto(Request $request)
    {
        $photoId = $request->input('id');
        $photo = ItemStoragePhoto::find($photoId)->delete();

        if ($photo) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function updateReviewStatus(Request $request)
    {
        try {
            $review = ItemStorageRating::find($request->id);

            if ($review) {
                $review->status = $request->status;
                $review->save();
                return response()->json(['status' => true, 'message' => 'Review status updated successfully.']);
            }
            return response()->json(['status' => false, 'message' => 'Review not found.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewReviewItemStorage($id)
    {
        try {
            $data = ItemStorage::where('id', $id)
                ->with(['photos', 'sellers'])
                ->first();

            if ($data) {
                $reviews = $data->ratings->all(); // Convert the collection to an array

                $currentPage = Paginator::resolveCurrentPage();
                $perPage = 5; // Number of items per page
                $currentItems = array_slice($reviews, ($currentPage - 1) * $perPage, $perPage);

                $paginator = new LengthAwarePaginator(
                    $currentItems,
                    count($reviews),
                    $perPage,
                    $currentPage,
                    ['path' => Paginator::resolveCurrentPath()]
                );
                return view('users.sellers.itemStorage.ratings', [
                    'data' => $data,
                    'paginator' => $paginator
                ]);
            } else {
                return response()->json(['status' => false, 'message' => 'Item not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }






}
