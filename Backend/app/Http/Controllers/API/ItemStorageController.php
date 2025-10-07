<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ItemStorageResource;
use App\Models\ItemStorageBlockDays;
use App\Models\ItemStorageFacilityOffer;
use App\Models\ItemStoragePhoto;
use App\Models\ItemStoragePivot;
use App\Models\ItemStorageRating;
use App\Models\ItemStorageTag;
use App\Models\ItemStorageTermsCondition;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemStorage;
use App\Http\Middleware\ValidateApiKey;
use Exception;
use Carbon\Carbon;

class ItemStorageController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateApiKey::class);
    }

    public function index()
    {

    }

    /**
     * @OA\Post(
     *     path="/api/V1/item-storage",
     *     summary="Create a new item storage",
     *     tags={"Item Storages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="listing_type", type="string", example="newwww"),
     *                 @OA\Property(property="categories_id", type="integer", example=1),
     *                 @OA\Property(property="sub_categories_id", type="integer", example=1),
     *                 @OA\Property(property="location", type="string", example="New York"),
     *                 @OA\Property(property="map_pin", type="string", example="40.7128,-74.0060"),
     *                 @OA\Property(property="exception_details", type="string", example="Some details about exceptions"),
     *                 @OA\Property(property="rate", type="number", format="float", example=100),
     *                 @OA\Property(property="rented_max_allow_days", type="integer", example=30),
     *                 @OA\Property(property="blocked_days", type="string", example="08/04/2024,08/22/2024,08/14/2024"),
     *                 @OA\Property(property="item_id", type="array", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="storage_photos", type="array", @OA\Items(type="file")),
     *                 @OA\Property(property="terms_conditions", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Term 1"),
     *                     @OA\Property(property="description", type="string", example="Description for term 1")
     *                 )),
     *                 @OA\Property(property="facility_offers", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Offer 1"),
     *                     @OA\Property(property="photo", type="file", format="binary"),
     *                     @OA\Property(property="description", type="string", example="Description for offer 1")
     *                 )),
     *                 @OA\Property(property="default_storage_photo", type="file"),
     *                 @OA\Property(property="description", type="string", example="asdsadasdasdasdasdasdas"),
     *                 @OA\Property(property="country", type="string", example="India"),
     *                 @OA\Property(property="state", type="string", example="GOA"),
     *                 @OA\Property(property="city", type="string", example="Goa"),
     *                 @OA\Property(property="pincode", type="string", example="321654"),
     *                 @OA\Property(property="landmark", type="string", example="Beach"),
     *                 @OA\Property(property="latitude", type="number", format="float", example=20.11234),
     *                 @OA\Property(property="longitude", type="number", format="float", example=87.99899787),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string", example="abc")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item Storage Created Successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="responseData", type="object",
     *                 @OA\Property(property="listing_type", type="string", example="newwww"),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="categories_id", type="integer", example=1),
     *                 @OA\Property(property="sub_categories_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="asdsadasdasdasdasdasdas"),
     *                 @OA\Property(property="location", type="string", example="New York"),
     *                 @OA\Property(property="exception_details", type="string", example="Some details about exceptions"),
     *                 @OA\Property(property="rate", type="string", example="100"),
     *                 @OA\Property(property="rented_max_allow_days", type="string", example="30"),
     *                 @OA\Property(property="country", type="string", example="null"),
     *                 @OA\Property(property="state", type="string", example="null"),
     *                 @OA\Property(property="city", type="string", example="null"),
     *                 @OA\Property(property="pincode", type="string", example="null"),
     *                 @OA\Property(property="landmark", type="string", example="null"),
     *                 @OA\Property(property="latitude", type="string", example="null"),
     *                 @OA\Property(property="longitude", type="string", example="null"),
     *                 @OA\Property(property="updated_at", type="string", example="05-08-2024"),
     *                 @OA\Property(property="created_at", type="string", example="05-08-2024"),
     *                 @OA\Property(property="id", type="integer", example=91)
     *             ),
     *             @OA\Property(property="message", type="string", example="Item Storage Created Successfully.")
     *         )
     *     )
     * )
     */


    public function store(Request $request)
    {
        requestLogStore($request->all(),'new_item_storage');

        try {
        $user_id = auth()->guard('api')->user()->id;

        $validator = Validator::make($request->all(), [
            'listing_type' => 'nullable|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'sub_categories_id' => 'required|exists:sub_categories,id',
            'location' => 'nullable|string|max:255',
            'exception_details' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
            'rate' => 'nullable|string|max:255',
            'rented_max_allow_days' => 'nullable|integer',
            'blocked_days' => 'nullable|string',
            'terms_conditions' => 'nullable|array',
            'facility_offers' => 'nullable|array',
            'default_storage_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'country'=>'nullable|string',
            'state'=>'nullable|string',
            'city'=>'nullable|string',
            'pincode'=>'nullable|string',
            'landmark'=>'nullable|string',
            'latitude'=>'nullable|string',
            'tags' => 'nullable|array',
            'longitude'=>'nullable|string',
            'item_id' => 'nullable|array',
            'storage_photos' => 'nullable|array',
        ], [
                'default_storage_photo.image' => 'The default storage photo must be an image.',
                'default_storage_photo.mimes' => 'The default storage photo must be a file of type: jpeg, png, jpg.',
                'default_storage_photo.max' => 'The default storage photo may not be greater than 2 MB.',
           ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, $validator->errors(), 'Form Validation Error', 400);
        }

        DB::beginTransaction();

            $validated = $validator->validated();

            $itemStorage = new ItemStorage();
            $itemStorage->listing_type = $validated['listing_type'];
            $itemStorage->user_id = $user_id;
            $itemStorage->categories_id = $validated['categories_id'];
            $itemStorage->sub_categories_id = $validated['sub_categories_id'];
            $itemStorage->description = $validated['description'];
            $itemStorage->location = $validated['location'];
            $itemStorage->exception_details = $validated['exception_details'];
            $itemStorage->rate = $validated['rate'];
            $itemStorage->rented_max_allow_days = $validated['rented_max_allow_days'];
            $itemStorage->country = $validated['country'];
            $itemStorage->state = $validated['state'];
            $itemStorage->city = $validated['city'];
            $itemStorage->pincode = $validated['pincode'];
            $itemStorage->landmark = $validated['landmark'];
            $itemStorage->latitude = $validated['latitude'];
            $itemStorage->longitude = $validated['longitude'];
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
                $thumbnailPath = $basePath . '/thumbnail';

                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                foreach ($request->file('storage_photos') as $photo) {
                    $path = $photo->store('item_storage', 'public');

                    $thumbnailPath = 'item_storage/thumbnail/' . basename($path);
                    $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($photo)->resize(1200, 800);
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

            if (count($validated['tags']) > 0) {
                foreach ($validated['tags'] as $tag) {
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
            return $this->sendResponse(true, $itemStorage, 'Item Storage Created Successfully.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]); // Added more context to the log
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemStorage $itemStorage)
    {
        return $itemStorage;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemStorage $itemStorage)
    {
        $validated = $request->validate([
            'listing_type' => 'nullable|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'sub_categories_id' => 'required|exists:sub_categories,id',
            'location' => 'nullable|string|max:255',
            'map_pin' => 'nullable|string|max:255',
            'exception_details' => 'nullable|string',
            'rate' => 'nullable|string|max:255',
            'rented_max_allow_days' => 'nullable|integer',
            'blocked_days' => 'nullable|integer',
        ]);

        $itemStorage->update($validated);
        return response()->json($itemStorage, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemStorage $itemStorage)
    {
        $itemStorage->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Post(
     *     path="/api/V1/get-item-storage",
     *     summary="Filter item storages",
     *     tags={"Item Storages"},
     *     security={{ "api_key": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="categories_id",
     *                 type="integer",
     *                 description="Category ID"
     *             ),
     *             @OA\Property(
     *                 property="sub_categories_id",
     *                 type="integer",
     *                 description="Subcategory ID"
     *             ),
     *             @OA\Property(
     *                 property="location",
     *                 type="string",
     *                 description="Location"
     *             ),
     *             @OA\Property(
     *                 property="date_from",
     *                 type="string",
     *                 format="date",
     *                 description="Start date (YYYY-MM-DD)"
     *             ),
     *             @OA\Property(
     *                 property="date_to",
     *                 type="string",
     *                 format="date",
     *                 description="End date (YYYY-MM-DD)"
     *             ),
     *             @OA\Property(
     *                 property="price_min",
     *                 type="number",
     *                 format="float",
     *                 description="Minimum price"
     *             ),
     *             @OA\Property(
     *                 property="price_max",
     *                 type="number",
     *                 format="float",
     *                 description="Maximum price"
     *             ),
     *             @OA\Property(
     *                 property="id",
     *                 type="number",
     *                 format="integer",
     *                 description="Item storage ID"
     *             ),
     *             @OA\Property(
     *                 property="limit",
     *                 type="number",
     *                 format="integer",
     *                 description="Limit number"
     *             ),
     *             @OA\Property(
     *                 property="page",
     *                 type="number",
     *                 format="integer",
     *                 description="Page number"
     *             ),
     *             @OA\Property(
     *                 property="listing_type",
     *                 type="string",
     *                 description="Listing type (e.g., Sale, Rent)"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=3
     *                     ),
     *                     @OA\Property(
     *                         property="listing_type",
     *                         type="string",
     *                         example="Sale"
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         example=8
     *                     ),
     *                     @OA\Property(
     *                         property="categories_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="sub_categories_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="location",
     *                         type="string",
     *                         example="New York"
     *                     ),
     *                     @OA\Property(
     *                         property="map_pin",
     *                         type="string",
     *                         example="40.7128,-74.0060"
     *                     ),
     *                     @OA\Property(
     *                         property="exception_details",
     *                         type="string",
     *                         example="Some details about exceptions"
     *                     ),
     *                     @OA\Property(
     *                         property="rate",
     *                         type="string",
     *                         example="100"
     *                     ),
     *                     @OA\Property(
     *                         property="rented_max_allow_days",
     *                         type="integer",
     *                         example=30
     *                     ),
     *                     @OA\Property(
     *                         property="blocked_days",
     *                         type="integer",
     *                         example=5
     *                     ),
     *                     @OA\Property(
     *                          property="max_allow_day_flag",
     *                          type="boolean",
     *                          example=true
     *                      ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="03-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="03-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="Electronics"
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="integer",
     *                             example=0
     *                         ),
     *                         @OA\Property(
     *                             property="deleted_at",
     *                             type="string",
     *                             example=null
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="sub_category",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="categories_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="TV"
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="integer",
     *                             example=0
     *                         ),
     *                         @OA\Property(
     *                             property="deleted_at",
     *                             type="string",
     *                             example=null
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(
     *                                 property="id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="item_name",
     *                                 type="string",
     *                                 example="keyboard"
     *                             ),
     *                             @OA\Property(
     *                                 property="category_id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="sub_category_id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="item_description",
     *                                 type="string",
     *                                 example="keyboard"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_photo",
     *                                 type="string",
     *                                 example="http://192.168.1.149:8002/storage/Item/1719927350.png"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_weight",
     *                                 type="string",
     *                                 example="30.00"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_price",
     *                                 type="string",
     *                                 example="900.00"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_condition",
     *                                 type="string",
     *                                 example="old"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_status",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="date_from",
     *                                 type="string",
     *                                 example="1970-01-01"
     *                             ),
     *                             @OA\Property(
     *                                 property="date_to",
     *                                 type="string",
     *                                 example="1970-01-01"
     *                             ),
     *                             @OA\Property(
     *                                 property="deleted_at",
     *                                 type="string",
     *                                 example=null
     *                             ),
     *                             @OA\Property(
     *                                 property="created_at",
     *                                 type="string",
     *                                 example="02-07-2024"
     *                             ),
     *                             @OA\Property(
     *                                 property="updated_at",
     *                                 type="string",
     *                                 example="02-07-2024"
     *                             ),
     *                             @OA\Property(
     *                                 property="pivot",
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="item_storage_id",
     *                                     type="integer",
     *                                     example=3
     *                                 ),
     *                                 @OA\Property(
     *                                     property="item_id",
     *                                     type="integer",
     *                                     example=1
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example=null
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid request data"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized access"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    public function filterItemStorage(Request $request)
    {
        try {
            $categories_id = $request->input('categories_id');
            $sub_categories_id = $request->input('sub_categories_id');
            $location = $request->input('location', '');
            $latitude = $request->input('latitude', '');
            $longitude = $request->input('longitude', '');
            $price_min = $request->input('price_min');
            $price_max = $request->input('price_max');
            $date_from = $request->input('date_from', '');
            $date_to = $request->input('date_to', '');
            $listing_type = $request->input('listing_type', '');
            $tags = $request->input('tags', '');
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $id = $request->input('id');

            $query = ItemStorage::with([
                'sellers.sellerDetails',
                'category',
                'subCategory',
                'items',
                'photos',
                'facilityOffers',
                'termsConditions',
                'tags',
                'sellers.activeGroups',
                'sellers.groupPivots.group.users',
                'ratings' => function($query) {
                    $query->select('id', 'seller_id', 'item_storage_id', 'rating', 'title','description', 'status', 'created_at')
                        ->with('seller:id,name,avatar')
                        ->where('status', 1);
                },
                'itemStorageBlockDays:id,item_storage_id,block_days_date'
                ])
                ->where('status', 1)
                ->orderBy('created_at', 'desc');


            // Apply filters
            if ($id) {
                $query->where('id', $id);
            }

            if ($categories_id) {
                $query->where('categories_id', $categories_id);
            }

            if ($listing_type) {
                $query->where('listing_type', 'like', '%' . $listing_type . '%');
            }

            if ($sub_categories_id) {
                $query->where('sub_categories_id', $sub_categories_id);
            }

            if ($location) {
                $query->where('location', 'like', '%' . $location . '%');
            }

            if ($price_min) {
                $query->where('rate', '>=', $price_min);
            }

            if ($price_max) {
                $query->where('rate', '<=', $price_max);
            }

            if ($date_from && $date_to) {
                $dateFrom = Carbon::parse($date_from);
                $dateTo = Carbon::parse($date_to);
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }

            if (!empty($tags)) {
                $query->whereHas('tags', function ($query) use ($tags) {
                    $query->where('tag_name', 'like', '%' . $tags . '%');
                });
            }

            // Get all item storages with the filters applied
            $itemStorages = $query->get();

            // Calculate distance if latitude and longitude are provided
            if ($latitude && $longitude) {
                $itemStorages->each(function ($itemStorage) use ($latitude, $longitude) {
                    $itemStorage->distance = $this->calculateDistance($latitude, $longitude, $itemStorage->latitude, $itemStorage->longitude);
                });
            } else {
                $nulldistance = [
                    'km' => 0,
                    'miles' => 0
                ];
                $itemStorages->each(function ($itemStorage) use($nulldistance) {
                    $itemStorage->distance = $nulldistance;
                });
            }

            $totalItems = $itemStorages->count();

            $totalPages = ceil($totalItems / $limit);

            $offset = ($page - 1) * $limit;

            $itemStorages = $itemStorages->slice($offset, $limit);

            $response = [
                'data' => ItemStorageResource::collection($itemStorages),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages
                ]
            ];

            if ($itemStorages->isEmpty()) {
                return $this->sendResponse(true, $response, 'No Item Storage found matching the criteria.', 200);
            }

            return $this->sendResponse(true, $response, 'Item Storage List.', 200);
        } catch (Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/V1/seller-item-storage",
     *     summary="Filter Seller item storages List",
     *     tags={"Seller Item Storages List"},
     *     security={{ "api_key": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="categories_id",
     *                 type="integer",
     *                 description="Category ID"
     *             ),
     *             @OA\Property(
     *                 property="sub_categories_id",
     *                 type="integer",
     *                 description="Subcategory ID"
     *             ),
     *             @OA\Property(
     *                 property="location",
     *                 type="string",
     *                 description="Location"
     *             ),
     *             @OA\Property(
     *                 property="date_from",
     *                 type="string",
     *                 format="date",
     *                 description="Start date (YYYY-MM-DD)"
     *             ),
     *             @OA\Property(
     *                 property="date_to",
     *                 type="string",
     *                 format="date",
     *                 description="End date (YYYY-MM-DD)"
     *             ),
     *             @OA\Property(
     *                 property="price_min",
     *                 type="number",
     *                 format="float",
     *                 description="Minimum price"
     *             ),
     *             @OA\Property(
     *                 property="price_max",
     *                 type="number",
     *                 format="float",
     *                 description="Maximum price"
     *             ),
     *             @OA\Property(
     *                 property="id",
     *                 type="number",
     *                 format="integer",
     *                 description="Item storage ID"
     *             ),
     *             @OA\Property(
     *                 property="limit",
     *                 type="number",
     *                 format="integer",
     *                 description="Limit number"
     *             ),
     *             @OA\Property(
     *                 property="page",
     *                 type="number",
     *                 format="integer",
     *                 description="Page number"
     *             ),
     *             @OA\Property(
     *                 property="listing_type",
     *                 type="string",
     *                 description="Listing type (e.g., Sale, Rent)"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=3
     *                     ),
     *                     @OA\Property(
     *                         property="listing_type",
     *                         type="string",
     *                         example="Sale"
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         example=8
     *                     ),
     *                     @OA\Property(
     *                         property="categories_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="sub_categories_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="location",
     *                         type="string",
     *                         example="New York"
     *                     ),
     *                     @OA\Property(
     *                         property="map_pin",
     *                         type="string",
     *                         example="40.7128,-74.0060"
     *                     ),
     *                     @OA\Property(
     *                         property="exception_details",
     *                         type="string",
     *                         example="Some details about exceptions"
     *                     ),
     *                     @OA\Property(
     *                         property="rate",
     *                         type="string",
     *                         example="100"
     *                     ),
     *                     @OA\Property(
     *                         property="rented_max_allow_days",
     *                         type="integer",
     *                         example=30
     *                     ),
     *                     @OA\Property(
     *                         property="blocked_days",
     *                         type="integer",
     *                         example=5
     *                     ),
     *                     @OA\Property(
     *                          property="max_allow_day_flag",
     *                          type="boolean",
     *                          example=true
     *                      ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="03-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="03-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="Electronics"
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="integer",
     *                             example=0
     *                         ),
     *                         @OA\Property(
     *                             property="deleted_at",
     *                             type="string",
     *                             example=null
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="sub_category",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="categories_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="TV"
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="integer",
     *                             example=0
     *                         ),
     *                         @OA\Property(
     *                             property="deleted_at",
     *                             type="string",
     *                             example=null
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             example="02-07-2024"
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(
     *                                 property="id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="item_name",
     *                                 type="string",
     *                                 example="keyboard"
     *                             ),
     *                             @OA\Property(
     *                                 property="category_id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="sub_category_id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="item_description",
     *                                 type="string",
     *                                 example="keyboard"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_photo",
     *                                 type="string",
     *                                 example="http://192.168.1.149:8002/storage/Item/1719927350.png"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_weight",
     *                                 type="string",
     *                                 example="30.00"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_price",
     *                                 type="string",
     *                                 example="900.00"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_condition",
     *                                 type="string",
     *                                 example="old"
     *                             ),
     *                             @OA\Property(
     *                                 property="item_status",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="date_from",
     *                                 type="string",
     *                                 example="1970-01-01"
     *                             ),
     *                             @OA\Property(
     *                                 property="date_to",
     *                                 type="string",
     *                                 example="1970-01-01"
     *                             ),
     *                             @OA\Property(
     *                                 property="deleted_at",
     *                                 type="string",
     *                                 example=null
     *                             ),
     *                             @OA\Property(
     *                                 property="created_at",
     *                                 type="string",
     *                                 example="02-07-2024"
     *                             ),
     *                             @OA\Property(
     *                                 property="updated_at",
     *                                 type="string",
     *                                 example="02-07-2024"
     *                             ),
     *                             @OA\Property(
     *                                 property="pivot",
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="item_storage_id",
     *                                     type="integer",
     *                                     example=3
     *                                 ),
     *                                 @OA\Property(
     *                                     property="item_id",
     *                                     type="integer",
     *                                     example=1
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example=null
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid request data"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized access"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="string",
     *                 example=null
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    public function filterSellerItemStorage(Request $request)
    {
        $user_id = auth()->guard('api')->user()->id;
        try {
            $categories_id = $request->input('categories_id');
            $sub_categories_id = $request->input('sub_categories_id');
            $location = $request->input('location', '');
            $latitude = $request->input('latitude', '');
            $longitude = $request->input('longitude', '');
            $price_min = $request->input('price_min');
            $price_max = $request->input('price_max');
            $date_from = $request->input('date_from', '');
            $date_to = $request->input('date_to', '');
            $listing_type = $request->input('listing_type', '');
            $tags = $request->input('tags', '');
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $id = $request->input('id');

            $query = ItemStorage::with([
                'sellers.sellerDetails',
                'category',
                'subCategory',
                'items',
                'photos',
                'facilityOffers',
                'termsConditions',
                'tags',
                'sellers.activeGroups',
                'sellers.groupPivots.group.users',
                'ratings' => function($query) {
                    $query->select('id', 'seller_id', 'item_storage_id', 'rating','title','description', 'status', 'created_at')
                        ->with('seller:id,name,avatar')
                        ->where('status', 1);
                },
                'itemStorageBlockDays:id,item_storage_id,block_days_date'
            ])
                ->where('status', 1)
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($id) {
                $query->where('id', $id);
            }

            if ($categories_id) {
                $query->where('categories_id', $categories_id);
            }

            if ($listing_type) {
                $query->where('listing_type', 'like', '%' . $listing_type . '%');
            }

            if ($sub_categories_id) {
                $query->where('sub_categories_id', $sub_categories_id);
            }

            if ($location) {
                $query->where('location', 'like', '%' . $location . '%');
            }

            if ($price_min) {
                $query->where('rate', '>=', $price_min);
            }

            if ($price_max) {
                $query->where('rate', '<=', $price_max);
            }

            if ($date_from && $date_to) {
                $dateFrom = Carbon::parse($date_from);
                $dateTo = Carbon::parse($date_to);
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }

            if (!empty($tags)) {
                $query->whereHas('tags', function ($query) use ($tags) {
                    $query->where('tag_name', 'like', '%' . $tags . '%');
                });
            }

            // Get all item storages with the filters applied
            $itemStorages = $query->get();

            // Calculate distance if latitude and longitude are provided
            if ($latitude && $longitude) {
                $itemStorages->each(function ($itemStorage) use ($latitude, $longitude) {
                    $itemStorage->distance = $this->calculateDistance($latitude, $longitude, $itemStorage->latitude, $itemStorage->longitude);
                });
            } else {
                $nulldistance = [
                    'km' => 0,
                    'miles' => 0
                ];
                $itemStorages->each(function ($itemStorage) use($nulldistance) {
                    $itemStorage->distance = $nulldistance;
                });
            }

            $totalItems = $itemStorages->count();

            $totalPages = ceil($totalItems / $limit);

            $offset = ($page - 1) * $limit;

            $itemStorages = $itemStorages->slice($offset, $limit);

            $response = [
                'data' => ItemStorageResource::collection($itemStorages),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages
                ]
            ];

            if ($itemStorages->isEmpty()) {
                return $this->sendResponse(true, $response, 'No Item Storage found matching the criteria.', 200);
            }

            return $this->sendResponse(true, $response, 'Item Storage List.', 200);
        } catch (Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadiusKm = 6371; // Radius of the Earth in km
        $earthRadiusMiles = 3963.19; // Radius of the Earth in miles

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDiff = $latTo - $latFrom;
        $lonDiff = $lonTo - $lonFrom;

        $a = sin($latDiff / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDiff / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distanceKm = $earthRadiusKm * $c; // Distance in km
        $distanceMiles = $earthRadiusMiles * $c; // Distance in miles

        return [
            'km' => $distanceKm,
            'miles' => $distanceMiles
        ];
    }


    /**
     * @OA\Post(
     *     path="/api/V1/give-rating",
     *     summary="Submit a rating for an item",
     *     description="Allows users to submit a rating for an item.",
     *     tags={"Ratings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="item_storage_id", type="string", example="43"),
     *             @OA\Property(property="rating", type="string", example="1"),
     *             @OA\Property(property="title", type="string", example="title1"),
     *             @OA\Property(property="description", type="string", example="storage not available at vadodara location."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating submitted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="responseData", type="object",
     *                 @OA\Property(property="item_storage_id", type="string", example="43"),
     *                 @OA\Property(property="seller_id", type="integer", example=10),
     *                 @OA\Property(property="rating", type="string", example="1"),
     *                 @OA\Property(property="status", type="integer", example=0),
     *                 @OA\Property(property="title", type="string", example="title1"),
     *                 @OA\Property(property="description", type="string", example="storage not available at vadodara location."),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-09T07:06:22.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-09T07:06:22.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=8)
     *             ),
     *             @OA\Property(property="message", type="string", example="Rating submitted successfully.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Form Validation Error"),
     *         ),
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function giveRating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_storage_id' => 'required|integer|exists:item_storages,id',
                'rating' => 'required|numeric|min:1|max:5',
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendResponse(false, $validator->errors(), 'Validation Error', 400);
            }

            $user_id = auth()->guard('api')->user()->id;

            $itemStorageRating = new ItemStorageRating();
            $itemStorageRating->item_storage_id = $request->item_storage_id;
            $itemStorageRating->seller_id = $user_id;
            $itemStorageRating->rating = $request->rating;
            $itemStorageRating->status = 0;
            $itemStorageRating->title = $request->title;
            $itemStorageRating->description = $request->description;
            $itemStorageRating->save();

            return $this->sendResponse(true, $itemStorageRating, 'Rating submitted successfully.', 200);
        } catch (Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }





}
