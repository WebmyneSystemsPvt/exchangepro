<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\ItemStorageController;
use App\Http\Controllers\API\MastersController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'V1'], function () {
    //Auth API outside auth
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    //Masters List
    Route::get('categories', [MastersController::class, 'categoryList']);
    Route::get('sub-categories', [MastersController::class, 'subCategoryList']);
    Route::get('languages', [MastersController::class, 'langList']);
    Route::get('banners', [MastersController::class, 'bannerList']);
    Route::get('items', [MastersController::class, 'itemList']);
    Route::get('get-faq', [MastersController::class, 'faqList']);
    Route::get('get-testimonials', [MastersController::class, 'testimonialsList']);

    //Item Storage API
    Route::post('get-item-storage', [ItemStorageController::class, 'filterItemStorage']);

    // Routes that require authentication via JWT
    Route::group(['middleware' => 'auth:api'], function () {
        //Auth API inside auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('get-profile-details', [AuthController::class, 'me']);
        Route::post('update-profile-details', [AuthController::class, 'updateProfile']);

        // Item Storage API
        Route::apiResource('item-storages', ItemStorageController::class);

        // Group API Routes
        Route::get('group-list', [GroupController::class, 'index']); // get group list
        Route::post('create-group', [GroupController::class, 'store']); // create group

        // Add rating /add review api
        Route::post('give-rating', [ItemStorageController::class, 'giveRating']); // create group

        // Item storage api for particular seller
        Route::post('seller-item-storage', [ItemStorageController::class, 'filterSellerItemStorage']);

    });
});

