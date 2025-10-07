<?php

namespace App\Http\Controllers\API;

use App\Models\BorrowerDetail;
use App\Models\ItemStoragePhoto;
use App\Models\SellerDetail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @OA\Info(
 *     title="Borrowss API",
 *     version="1.0.0"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="API Endpoints of Authentication"
 * )
 */

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/V1/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Create a new user with name, email, password, and role",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the user",
     *                 example="manoj"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 description="Email address of the user",
     *                 example="mk123@gmail.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="Password of the user",
     *                 example="manoj@123"
     *             ),
     *             @OA\Property(
     *                 property="role",
     *                 type="string",
     *                 description="Role of the user",
     *                 example="borrower"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="manoj"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="mk123@gmail.com"
     *                 ),
     *                 @OA\Property(
     *                      property="avatar",
     *                      type="string",
     *                      example="http://192.168.1.149:8002/storage/avatar/avatar.jpg"
     *                  ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date",
     *                     example="28-06-2024"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date",
     *                     example="28-06-2024"
     *                 ),
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=78
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User Created Successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error"
     *             )
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                    'required',
                    'min:6',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
             ],
            'role' => 'required|exists:roles,id'
        ],
        [
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters long.',
            'password.required_with' => 'The password confirmation is required when password is present.',
            'password.same' => 'The password and password confirmation must match.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
            // Add other custom messages for other fields if needed
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, $validator->errors(), 'Form Validation Error', 400);
        }

        try {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->avatar = 'avatar/avatar.jpg';
            $user->save();

            if (isset($request->role)) {
                $user->assignRole($request->role);
            }

            if($user){
                if($request->role == 2){
                    $BorrowerDetail = new BorrowerDetail();
                    $BorrowerDetail->user_id = $user->id;
                    $BorrowerDetail->save();
                }
                if($request->role == 3){
                    $sellerDetail = new SellerDetail();
                    $sellerDetail->user_id = $user->id;
                    $sellerDetail->company_name = $request->input('company_name', '');
                    $sellerDetail->save();
                }
            }
            $user['role_id'] = $user->roles->first()->id;
            unset($user['roles']);
            return $this->sendResponse(true, $user, 'User Created Successfully.', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/V1/login",
     *     tags={"Auth"},
     *     summary="Login a user",
     *     description="Login a user with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 description="Email address of the user",
     *                 example="admin@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="Password of the user",
     *                 example="password"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="object",
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string",
     *                     example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     *                 ),
     *                 @OA\Property(
     *                     property="token_type",
     *                     type="string",
     *                     example="bearer"
     *                 ),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="integer",
     *                     example=3600
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=66
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Admin User"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="admin@example.com"
     *                     ),
     *                     @OA\Property(
     *                         property="email_verified_at",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="stages",
     *                         type="integer",
     *                         example=0
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date",
     *                         example="27-06-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date",
     *                         example="27-06-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="role_id",
     *                         type="integer",
     *                         example=2
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Login Successful"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid email or password"
     *             )
     *         )
     *     )
     * )
     */


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, $validator->errors(), 'Login Validation Error', 400);
        }

        try {
            $userdata = User::where('email', $request->email)->first();
            if($userdata->status === 0){
                return $this->sendResponse(false, [], 'User is registered but not active, please contact admin.', 404);
            }

            $user = User::where('email', $request->email)->where('status',1)->first();

            if (!Hash::check($request->password, $user->password)) {
                return $this->sendResponse(false, [], 'Password does not match', 401);
            }

            if($user->roles->first()->id != $request->role){
                return $this->sendResponse(false, [], 'Invalid login credentials.', 404);
            }

            if (!$token = auth()->guard('api')->attempt($request->only('email', 'password'))) {
                return $this->sendResponse(false, [], 'Unauthorized', 401);
            }
            $user['role_id'] = $user->roles->first()->id;
            unset($user['roles']);

            return $this->respondWithToken($token, $user);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }


    protected function respondWithToken($token, $user)
    {
        return $this->sendResponse(true, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'user' => $user
        ], 'Login Successful', 200);
    }


    public function logout() // pass jwtToken in Header
    {
        try {
            auth()->guard('api')->logout();
            return $this->sendResponse(true, [], 'Successfully logged out', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    public function refresh() // pass jwtToken in Header
    {
        try {
            return $this->respondWithToken(auth()->guard('api')->refresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/V1/get-profile-details",
     *      operationId="getProfileDetails",
     *      tags={"Auth"},
     *      summary="Get user profile details",
     *      description="Returns the profile details of the authenticated user.",
     *      security={{"api_key": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="responseData", type="object",
     *                  @OA\Property(property="id", type="integer", example=9),
     *                  @OA\Property(property="name", type="string", example="borrower"),
     *                  @OA\Property(property="email", type="string", example="borrowers@borrowers.com"),
     *                  @OA\Property(property="email_verified_at", type="string", example=null),
     *                  @OA\Property(property="status", type="integer", example=1),
     *                  @OA\Property(property="stages", type="integer", example=0),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="created_at", type="string", example="04-07-2024"),
     *                  @OA\Property(property="updated_at", type="string", example="04-07-2024"),
     *                  @OA\Property(property="roles", type="array", @OA\Items(type="string", example="borrower")),
     *                  @OA\Property(property="role_id", type="integer", example=2),
     *                  @OA\Property(property="profile_details", type="object",
     *                      @OA\Property(property="id", type="integer", example=4),
     *                      @OA\Property(property="user_id", type="integer", example=9),
     *                      @OA\Property(property="city", type="string", example="abc"),
     *                      @OA\Property(property="address", type="string", example="asdasd"),
     *                      @OA\Property(property="pincode", type="string", example="654987"),
     *                      @OA\Property(property="location", type="string", example="Ahmedabad"),
     *                      @OA\Property(property="phone_number", type="string", example="2132132"),
     *                      @OA\Property(property="created_at", type="string", example="2024-07-04T05:42:48.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", example="2024-07-04T06:15:50.000000Z"),
     *                      @OA\Property(property="deleted_at", type="string", example=null)
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="User details retrieved successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      )
     * )
     */


    public function me()
    {
        try {
            $user = auth()->guard('api')->user();
            if ($user) {
                $user->load('roles');
                $user->load('borrowerDetails');
                $user->load('sellerDetails');
                $roleNames = $user->roles->pluck('name')->toArray();
                $profileDetails = null;
                $userData = $user->toArray();
                if (in_array('borrower', $roleNames)) {
                    $profileDetails = $user->borrowerDetails;
                } elseif (in_array('seller', $roleNames)) {
                    $profileDetails = $user->sellerDetails;
                }
                $userData['roles'] = $roleNames;
                $userData['role_id'] = $user->roles->first()->id;
                if ($profileDetails) {
                    $userData['profile_details'] = $profileDetails->toArray();
                }
                unset($userData['borrower_details']);
                unset($userData['seller_details']);
                return $this->sendResponse(true, $userData, 'User details retrieved successfully', 200);
            }

            return $this->sendResponse(false, [], 'User not found.', 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/V1/update-profile-details",
     *      operationId="updateProfileDetails",
     *      tags={"Auth"},
     *      summary="Update user profile details",
     *      description="Updates the profile details of the authenticated user.",
     *      security={{"api_key": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "city", "address", "pincode", "location", "phone_number"},
     *              @OA\Property(property="name", type="string", example="borrower"),
     *              @OA\Property(property="email", type="string", format="email", example="borrowers@borrowers.com"),
     *              @OA\Property(property="city", type="string", example="abc"),
     *              @OA\Property(property="address", type="string", example="asdasd"),
     *              @OA\Property(property="pincode", type="string", example="654987"),
     *              @OA\Property(property="location", type="string", example="Ahmedabad"),
     *              @OA\Property(property="phone_number", type="string", example="8866558844"),
     *              @OA\Property(property="avatar", type="file", example="")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="responseData", type="object",
     *                  @OA\Property(property="id", type="integer", example=9),
     *                  @OA\Property(property="name", type="string", example="borrower"),
     *                  @OA\Property(property="email", type="string", example="borrowers@borrowers.com"),
     *                  @OA\Property(property="avatar", type="string", example="http://192.168.1.149:8002/storage/avatar/anxXVKfWppomo6z6oSa1DMWczKRKyeCqAwACGh23.png"),
     *                  @OA\Property(property="email_verified_at", type="string", example=null),
     *                  @OA\Property(property="status", type="integer", example=1),
     *                  @OA\Property(property="stages", type="integer", example=0),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="created_at", type="string", example="04-07-2024"),
     *                  @OA\Property(property="updated_at", type="string", example="04-07-2024"),
     *                  @OA\Property(property="roles", type="array", @OA\Items(type="string", example="borrower")),
     *                  @OA\Property(property="profile_details", type="object",
     *                      @OA\Property(property="id", type="integer", example=4),
     *                      @OA\Property(property="user_id", type="integer", example=9),
     *                      @OA\Property(property="city", type="string", example="abc"),
     *                      @OA\Property(property="address", type="string", example="asdasd"),
     *                      @OA\Property(property="pincode", type="string", example="654987"),
     *                      @OA\Property(property="location", type="string", example="Ahmedabad"),
     *                      @OA\Property(property="phone_number", type="string", example="8866558844"),
     *                      @OA\Property(property="created_at", type="string", example="2024-07-04T05:42:48.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", example="2024-07-04T06:25:29.000000Z"),
     *                      @OA\Property(property="deleted_at", type="string", example=null)
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="User updated successfully.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *          )
     *      )
     * )
     */

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->guard('api')->user();
            $user->load('roles');
            $roleNames = $user->roles->pluck('name')->toArray();

            DB::beginTransaction();

            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'city' => 'required',
                'pincode' => 'required|numeric|digits:6',
                'location' => 'required',
                'phone_number' => 'required|numeric|digits:10',
                'address' => 'required'
            ];

            if ($request->hasFile('avatar')) {
                $basePath = storage_path('app/public/avatar');
                $thumbnailPath = $basePath . '/thumbnail';

                if (!File::exists($thumbnailPath)) {
                    File::makeDirectory($thumbnailPath, 0755, true);
                }

                $path = $request->file('avatar')->store('avatar', 'public');

                $thumbnailPath = 'avatar/thumbnail/' . basename($path);
                $image = \Intervention\Image\Facades\Image::configure(['driver' => 'Gd'])->make($request->file('avatar'))->resize(100, 100);
                $image->save(storage_path('app/public/' . $thumbnailPath));

                $user->avatar = $path;
            }

            if (in_array(config('constants.SELLER'), $roleNames)) {
                $rules['company_name'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendResponse(false, [], $validator->errors()->all(), 422);
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->save();

            $userData = $user->toArray();
            $userData['roles'] = $roleNames;

            if (in_array(config('constants.BORROWER'), $roleNames)) {
                $borrowerDetail = BorrowerDetail::where('user_id', $user->id)->first();
                if ($borrowerDetail) {
                    $borrowerDetail->city = $request->input('city');
                    $borrowerDetail->address = $request->input('address');
                    $borrowerDetail->pincode = $request->input('pincode');
                    $borrowerDetail->location = $request->input('location');
                    $borrowerDetail->phone_number = $request->input('phone_number');
                    $borrowerDetail->save();

                    $userData['profile_details'] = $borrowerDetail->toArray();
                } else {
                    return $this->sendResponse(false, [], 'Borrower details not found.', 404);
                }
            } elseif (in_array(config('constants.SELLER'), $roleNames)) {
                $sellerDetail = SellerDetail::where('user_id', $user->id)->first();
                if ($sellerDetail) {
                    $sellerDetail->company_name = $request->input('company_name');
                    $sellerDetail->city = $request->input('city');
                    $sellerDetail->address = $request->input('address');
                    $sellerDetail->pincode = $request->input('pincode');
                    $sellerDetail->location = $request->input('location');
                    $sellerDetail->phone_number = $request->input('phone_number');
                    $sellerDetail->save();

                    $userData['profile_details'] = $sellerDetail->toArray();
                } else {
                    return $this->sendResponse(false, [], 'Seller details not found.', 404);
                }
            }

            DB::commit();

            return $this->sendResponse(true, $userData, 'Profile Updated Successfully.', 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }
}
