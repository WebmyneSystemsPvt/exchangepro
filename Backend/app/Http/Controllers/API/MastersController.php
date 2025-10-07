<?php

namespace App\Http\Controllers\API;

use App\Models\FAQ;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Category;
use App\Models\Language;
use App\Http\Middleware\ValidateApiKey;
use App\Models\Bannerslider;
/**
 * @OA\Tag(
 *     name="Masters",
 *     description="API Endpoints of Masters"
 * )
 */
class MastersController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateApiKey::class);
    }
    /**
     * @OA\Get(
     *     path="/api/V1/categories",
     *     tags={"Masters"},
     *     summary="Get all categories with sub-categories",
     *     description="Retrieve all categories along with their sub-categories",
     *     @OA\Response(
     *         response=200,
     *         description="A list of categories with sub-categories",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="cate1"
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="integer",
     *                         example=0
     *                     ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date",
     *                         example="02-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date",
     *                         example="02-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="sub_categories",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(
     *                                 property="id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="categories_id",
     *                                 type="integer",
     *                                 example=1
     *                             ),
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="string",
     *                                 example="sub cate1"
     *                             ),
     *                             @OA\Property(
     *                                 property="status",
     *                                 type="integer",
     *                                 example=0
     *                             ),
     *                             @OA\Property(
     *                                 property="deleted_at",
     *                                 type="string",
     *                                 nullable=true,
     *                                 example=null
     *                             ),
     *                             @OA\Property(
     *                                 property="created_at",
     *                                 type="string",
     *                                 format="date",
     *                                 example="02-07-2024"
     *                             ),
     *                             @OA\Property(
     *                                 property="updated_at",
     *                                 type="string",
     *                                 format="date",
     *                                 example="02-07-2024"
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example=""
     *             )
     *         )
     *     )
     * )
     */

    public function categoryList() {

        try {

            $Category = Category::with('subCategories')->get();
            return $this->sendResponse(true, $Category, '', 200);

        } catch (\Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/V1/sub-categories",
     *      operationId="getSubCategories",
     *      tags={"Masters"},
     *      summary="Get list of sub-categories",
     *      description="Returns a list of sub-categories with their details.",
     *      security={{"api_key": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="responseData", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="categories_id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="TV"),
     *                      @OA\Property(property="status", type="integer", example=0),
     *                      @OA\Property(property="deleted_at", type="string", example=null),
     *                      @OA\Property(property="created_at", type="string", example="02-07-2024"),
     *                      @OA\Property(property="updated_at", type="string", example="02-07-2024")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Sub Categories List")
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

    public function subCategoryList() {

        try {

            $Category = SubCategory::get();
            return $this->sendResponse(true, $Category, 'Sub Categories List', 200);

        } catch (\Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }




    /**
     * @OA\Get(
     *     path="/api/V1/languages",
     *     tags={"Masters"},
     *     summary="Get all languages",
     *     description="Retrieve all available languages",
     *     @OA\Response(
     *         response=200,
     *         description="A list of languages",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="responseData",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="English"
     *                     ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date",
     *                         example="02-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date",
     *                         example="02-07-2024"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example=""
     *             )
     *         )
     *     )
     * )
     */

    public function langList() {

        try {
            $Language = Language::all();
            return $this->sendResponse(true, $Language, '', 200);
        } catch (\Exception $e) {
             return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/V1/banners",
 *     tags={"Masters"},
 *     summary="Retrieve list of banners",
 *     @OA\Response(
 *         response=200,
 *         description="A list with banners",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(
 *                 property="responseData",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="banner1"),
 *                     @OA\Property(property="photo", type="string", example=""),
 *                     @OA\Property(property="description", type="string", example="dsfdsf"),
 *                     @OA\Property(property="status", type="integer", example=0),
 *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-02T11:52:07.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-02T11:52:10.000000Z")
 *                 )
 *             ),
 *             @OA\Property(property="message", type="string", example="")
 *         )
 *     )
 * )
 */


    public function bannerList()
    {
        try {
            $data = Bannerslider::all()->chunk(4)->map->values()->all();
            return $this->sendResponse(true, $data, 'banner list', 200);
        } catch (\Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/V1/items",
     *     summary="Get a list of items",
     *     tags={"Masters"},
     *     security={{"api_key": {}}},
     *     @OA\Parameter(
     *         name="X-Api-Key",
     *         in="header",
     *         required=true,
     *         description="API Key",
     *         @OA\Schema(
     *             type="string"
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
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="item_name",
     *                         type="string",
     *                         example="keyboard"
     *                     ),
     *                     @OA\Property(
     *                         property="category_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="sub_category_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="item_description",
     *                         type="string",
     *                         example="keyboard"
     *                     ),
     *                     @OA\Property(
     *                         property="item_photo",
     *                         type="string",
     *                         example="1719927350.png"
     *                     ),
     *                     @OA\Property(
     *                         property="item_weight",
     *                         type="string",
     *                         example="30.00"
     *                     ),
     *                     @OA\Property(
     *                         property="item_price",
     *                         type="string",
     *                         example="900.00"
     *                     ),
     *                     @OA\Property(
     *                         property="item_condition",
     *                         type="string",
     *                         example="old"
     *                     ),
     *                     @OA\Property(
     *                         property="item_status",
     *                         type="string",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="date_from",
     *                         type="string",
     *                         example="1970-01-01"
     *                     ),
     *                     @OA\Property(
     *                         property="date_to",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="deleted_at",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="02-07-2024"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="02-07-2024"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Items List."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     )
     * )
     */

    public function itemList() {

        try {

            $Item = Item::all();
            return $this->sendResponse(true, $Item, 'Items List.', 200);

        } catch (\Exception $e) {

            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }

    }

    /**
     * @OA\Get(
     *      path="/api/V1/get-faq",
     *      operationId="getFAQs",
     *      tags={"FAQs"},
     *      summary="Get all FAQs",
     *      description="Returns a list of all FAQs.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="responseData",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="question", type="string", example="abc"),
     *                      @OA\Property(property="answer", type="string", example="yes"),
     *                      @OA\Property(property="order", type="integer", example=2),
     *                      @OA\Property(property="deleted_at", type="string", example=null),
     *                      @OA\Property(property="created_at", type="string", example="16-07-2024"),
     *                      @OA\Property(property="updated_at", type="string", example="16-07-2024")
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="FAQ List."
     *              )
     *          )
     *      )
     * )
     */
    public function faqList() {
        try {
            $FAQ = FAQ::orderBy('order','asc')->get();
            return $this->sendResponse(true, $FAQ, 'FAQ List.', 200);
        } catch (\Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="API Documentation",
     *      description="Swagger OpenAPI documentation for Laravel API",
     *      @OA\Contact(
     *          email="support@example.com"
     *      ),
     * )
     */

    /**
     * @OA\Get(
     *      path="/api/V1/get-testimonials",
     *      operationId="getTestimonials",
     *      tags={"Testimonials"},
     *      summary="Get list of testimonials",
     *      description="Returns list of testimonials",
     *      @OA\Parameter(
     *          name="X-Api-Key",
     *          in="header",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(
     *                  property="responseData",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="user_name", type="string", example="asdasdasdas"),
     *                      @OA\Property(property="user_position", type="string", example="asd"),
     *                      @OA\Property(property="user_company", type="string", example="asdad"),
     *                      @OA\Property(property="testimonial", type="string", example="asdasdad"),
     *                      @OA\Property(property="photo", type="string", example="http://192.168.1.149:8002/storage/testimonial_photos/0wAdKTOgw5d2YXgs81UmoFKpiPlxsVSdFVJftS39.jpg"),
     *                      @OA\Property(property="deleted_at", type="string", nullable=true, example=null),
     *                      @OA\Property(property="created_at", type="string", example="16-07-2024"),
     *                      @OA\Property(property="updated_at", type="string", example="16-07-2024")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Testimonial List.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      ),
     * )
     */
    public function testimonialsList() {
        try {
            $FAQ = Testimonial::all();
            return $this->sendResponse(true, $FAQ, 'Testimonial List.', 200);
        } catch (\Exception $e) {
            return $this->sendResponse(false, [], $e->getMessage(), 500);
        }
    }




}
