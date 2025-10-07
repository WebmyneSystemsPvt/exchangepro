<?php

namespace App\Http\Controllers\API;

use App\Models\Groups;
use App\Models\GroupsDocuments;
use App\Models\GroupsPivotes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\ValidateApiKey;
use Exception;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateApiKey::class);
    }
    /**
     * @OA\Get(
     *     path="/api/V1/group-list",
     *     summary="Get list of groups",
     *     tags={"Group"},
     *     security={{"api_key": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Group List",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="responseData", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="asdas"),
     *                     @OA\Property(property="documents", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="group_document", type="string", example="http://192.168.1.149:8002/storage/group_documents/4eJdPsHvcCNjdMiter7ecXGlysfFqPbmcoA19yWq.png"),
     *                             @OA\Property(property="group_id", type="integer", example=2)
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Group List.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Groups::with(['documents' => function($query) {
                $query->select('id', 'group_document', 'group_id');
            }])
            ->select('id','name')
            ->where('status', 1)
            ->orderBy('created_at', 'desc');

            if ($request->filled('created_by')) {
                $query->where('group.created_by', $request->created_by);
            }

            if ($request->filled('status')) {
                $query->where('group.status', $request->status);
            }

            $data = $query->get();

            if ($data->isEmpty()) {
                return $this->sendResponse(true, [], 'No group found matching the criteria.', 404);
            }

            return $this->sendResponse(true, $data, 'Group List.', 200);
        } catch (Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/V1/create-group",
     *     summary="Create a new group",
     *     tags={"Group"},
     *     security={{"api_key": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the group"
     *                 ),
     *                 @OA\Property(
     *                     property="group_id",
     *                     type="integer",
     *                     description="ID of an existing group if updating"
     *                 ),
     *                 @OA\Property(
     *                     property="group_document[]",
     *                     type="array",
     *                     @OA\Items(type="file", format="file", description="Group document(s)")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Group saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="responseData", type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="abc"),
     *                 @OA\Property(property="created_by", type="integer", example=8),
     *                 @OA\Property(property="status", type="integer", example=0),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="created_at", type="string", example="09-07-2024"),
     *                 @OA\Property(property="updated_at", type="string", example="09-07-2024")
     *             ),
     *             @OA\Property(property="message", type="string", example="Group saved successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object", example={
     *                 "name": {"The name field is required."},
     *                 "group_id": {"The group_id field is required."},
     *                 "group_document": {"The group_document field is required."}
     *             }),
     *             @OA\Property(property="message", type="string", example="Form Validation Error")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;
        if (!$user->hasRole('seller')) {
            return $this->sendResponse(false, [], 'Unauthorized: Only sellers can perform this action.', 403);
        }
        $rules = [];
        if ($request->has('group_id')) {
            $rules['group_id'] = 'required|exists:groups,id';
            $rules['group_document'] = 'required|array';
        } else {
            $rules['name'] = 'required|unique:groups,name';
            $rules['group_document'] = 'required|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendResponse(false, $validator->errors(), 'Form Validation Error', 400);
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();

            if ($request->has('group_id')) {
                $group = Groups::find($validated['group_id']);
                if ($group && $request->has('group_document')) {
                    GroupsDocuments::where('group_id', $group->id)->delete();

                    foreach ($request->file('group_document') as $photo) {
                        $path = $photo->store('group_documents', 'public');
                        $groupDocument = new GroupsDocuments();
                        $groupDocument->group_document = $path;
                        $groupDocument->group_id = $group->id;
                        $groupDocument->save();

                        $groupPivote = new GroupsPivotes();
                        $groupPivote->group_id = $group->id;
                        $groupPivote->seller_id = $user_id;
                        $groupPivote->document_id = $groupDocument->id;
                        $groupPivote->save();
                    }
                }
            } else {
                $group = new Groups();
                $group->name = $validated['name'];
                $group->created_by = $user_id;
                $group->status = 0;
                $group->save();
                foreach ($request->file('group_document') as $photo) {
                    $path = $photo->store('group_documents', 'public');
                    $groupDocument = new GroupsDocuments();
                    $groupDocument->group_document = $path;
                    $groupDocument->group_id = $group->id;
                    $groupDocument->save();
                }
            }
            DB::commit();
            return $this->sendResponse(true, $group, 'Group saved successfully.', 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Groups $itemStorage)
    {
        return $itemStorage;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groups $itemStorage)
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
    public function destroy(Groups $itemStorage)
    {
        $itemStorage->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
