<?php

use App\Http\Controllers\FAQController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\BorrowersController;
use App\Http\Controllers\SellersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Masters\GenderController;
use App\Http\Controllers\Masters\LanguageController;
use App\Http\Controllers\Masters\ItemController;
use App\Http\Controllers\Masters\CategoryController;
use App\Http\Controllers\Masters\SubCategoryController;
use App\Http\Controllers\Masters\BannerController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/clear', function() {
    Artisan::call('optimize:clear');
    return "All Cache cleared successfully.!";
});

Route::get('/storagelink', function() {
    $exitCode = Artisan::call('storage:link');
    return "Storage Link Created successfully.!";
});

Route::get('/jwtSecret', function() {
    $exitCode = Artisan::call('jwt:secret');
    return "JWT :secret. Generated.";
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/error-log-view', [LogController::class, 'showLogs'])->name('show.logs');
Route::get('/json-log-list', [LogController::class, 'listJsonFiles'])->name('json.log.list');
Route::get('/view-json/{filename}', [LogController::class, 'viewJsonFile'])->name('view.json.file');
Route::post('/error-log-remove', [LogController::class, 'clearLogs'])->name('clear.logs');

Route::group(['middleware' => ['auth']], function() {
    //Home Route / Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    //Profile Route
    Route::get('/profile', [GeneralController::class, 'profile'])->name('profile');

    //------------------------------------------------User Management Routes Start------------------------------------------------------//
    // Borrowers Routes
    Route::get('/users', [BorrowersController::class, 'index'])->name('usersListing'); // List Page
    Route::get('/users-data', [BorrowersController::class, 'getUsers'])->name('users.data'); // Get List
    Route::get('/createUser', [BorrowersController::class, 'create'])->name('createUser'); // Create Form
    Route::post('userstore', [BorrowersController::class, 'store'])->name('userstore'); // Store Create Form Data
    Route::get('/users/{id}', [BorrowersController::class, 'getEdit'])->name('user.edit'); // Get Edit Data
    Route::post('/users', [BorrowersController::class, 'update'])->name('user.update'); // Update Edit Data
    Route::delete('users/{id}', [BorrowersController::class, 'delete'])->name('delete'); // Delete Record

    // Sellers Routes
    Route::get('/sellerusers', [SellersController::class, 'index'])->name('sellerusersListing'); // List Page
    Route::get('/sellerusers-data', [SellersController::class, 'getUsers'])->name('sellerusers.data'); // Get List
    Route::get('/sellercreateUser', [SellersController::class, 'create'])->name('sellercreateUser'); // Create Form
    Route::post('selleruserstore', [SellersController::class, 'store'])->name('selleruserstore'); // Store Create Form Data
    Route::get('/sellerusers/{id}', [SellersController::class, 'getEdit'])->name('selleruser.edit'); // Get Edit Data
    Route::post('/sellerusers', [SellersController::class, 'update'])->name('selleruser.update'); // Update Edit Data
    Route::delete('sellerusers/{id}', [SellersController::class, 'delete'])->name('sellerdelete'); // Delete Record
    //------------------------------------------------User Management Routes End------------------------------------------------------//

    //------------------------------------------------Masters Routes Start------------------------------------------------------//

    //Genders Route
    Route::resource('genders', GenderController::class);
    Route::get('/gender-data', [GenderController::class, 'getGenders'])->name('gendersData'); // Get List

    Route::resource('banners', BannerController::class);
    Route::get('/banner-data', [BannerController::class, 'getBanners'])->name('bannersData'); // Get List

    //Languages Route
    Route::resource('languages', LanguageController::class);
    Route::get('/language-data', [LanguageController::class, 'getLanguages'])->name('languagesData'); // Get List

    //Category Route
    Route::resource('categories', CategoryController::class);
    Route::get('/categories-data', [CategoryController::class, 'getCategories'])->name('categoriesData');

    //Sub Category Route
    Route::resource('subCategories', SubCategoryController::class);
    Route::get('/subCategories-data', [SubCategoryController::class, 'getSubCategories'])->name('subCategoriesData');

    //Items Route
    Route::resource('items', ItemController::class);
    Route::get('/items-data', [ItemController::class, 'getItems'])->name('itemsData'); // Get List

    //Settings Route
    Route::resource('settings', SettingController::class);
    Route::get('/settings-data', [SettingController::class, 'getSettings'])->name('settingsData'); // Get List

    //------------------------------------------------Masters Routes End------------------------------------------------------//
    //Group Routes
    Route::get('/group-list', [GroupController::class, 'groupList'])->name('groupList');
    Route::get('/group-add', [GroupController::class, 'create'])->name('groupAdd');
    Route::post('/group-status', [GroupController::class, 'updateStatus'])->name('group.status');
    Route::post('/group-store', [GroupController::class, 'store'])->name('group.store');
    Route::get('groupEdit/{id}', [GroupController::class, 'getEdit'])->name('getEdit');
    Route::delete('groupdelete/{id}', [GroupController::class, 'delete'])->name('groupdelete');
    Route::post('/group/update/{id}', [GroupController::class, 'update'])->name('group.update');
    Route::get('/group-request-list/{group_id}', [GroupController::class, 'groupRequestList'])->name('group.request.list');
    Route::post('/group-approve-status', [GroupController::class, 'updateGroupMemberStatus'])->name('group.approve.status');

    //FAQ Routes
    Route::get('/faqs', [FAQController::class, 'index'])->name('faqs.index');// List FAQs
    Route::get('/faqs/create', [FAQController::class, 'create'])->name('faqs.create');// Create FAQ
    Route::post('/faqs', [FAQController::class, 'store'])->name('faqs.store');
    Route::get('/faqsEdit/{id}', [FAQController::class, 'getEdit'])->name('faqs.getEdit');// Edit FAQ
    Route::put('/faqs/{faq}', [FAQController::class, 'update'])->name('faqs.update');
    Route::post('/faqs/update-order', [FAQController::class, 'updateOrder'])->name('faqs.updateOrder');
    Route::get('/faqs/load', [FAQController::class, 'load'])->name('faqs.load');
    Route::delete('/faqs/{testimonial}', [FAQController::class, 'destroy'])->name('faqs.destroy'); // Delete testimonial

    //Testimonials Routes
    Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index'); // List testimonials
    Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create'); // Create testimonial form
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store'); // Store new testimonial
    Route::get('/testimonials/{testimonial}', [TestimonialController::class, 'edit'])->name('testimonials.edit'); // Edit testimonial form
    Route::put('/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update'); // Update testimonial
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy'); // Delete testimonial

    //------------------------------------------------Single data Route Start---------------------------------------------//
    Route::get('/get-subcategories/{category_id}', [GeneralController::class, 'getSubcategories'])->name('get-subcategories');
    Route::get('/seller-item-storage-list/{seller_id}', [SellersController::class, 'sellerItemStorageList'])->name('seller.item.storage.list');
    Route::post('/seller-item-storage-update-status', [SellersController::class, 'updateStatus'])->name('seller.item.storage.update.status');
    Route::get('/seller-item-storage-details/{id}', [SellersController::class, 'sellerItemStorageDetails'])->name('seller.item.storage.details');
    Route::get('/pending-item-storage-list', [SellersController::class, 'pendingItemStorageList'])->name('pending.item.storage.list');

    Route::get('/add-item-storage', [SellersController::class, 'addItemStorage'])->name('add.item.storage');
    Route::post('/store-item-storage', [SellersController::class, 'storeItemStorage'])->name('store.item.storage');
    Route::get('/edit-item-storage/{id}', [SellersController::class, 'editItemStorage'])->name('edit.item.storage');
    Route::post('/update-item-storage', [SellersController::class, 'updateItemStorage'])->name('update.item.storage');
    Route::post('/remove-single-item-storage-photo', [SellersController::class, 'deletePhoto']);
    Route::post('/update-review-status', [SellersController::class, 'updateReviewStatus']);
    Route::get('/view-review-item-storage/{id}', [SellersController::class, 'viewReviewItemStorage'])->name('view.review.item.storage');
    Route::get('/item-storage/reviews', [SellersController::class, 'loadMoreReviews'])->name('item.storage.reviews');

    //------------------------------------------------Get Single data Route End-----------------------------------------------//

    //Role & Permissions Routes for web
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');
});


