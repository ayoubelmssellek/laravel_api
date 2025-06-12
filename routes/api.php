<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CategorieController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Admin\AdminActionsController;
use App\Http\Controllers\SpecialOfferController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Carbon;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;





Route::get('/user',[AuthController::class,'getUserData'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);
Route::delete('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');




Route::middleware(['auth:sanctum', 'admin'])->group(function (){
    Route::apiResource('/categorie', CategorieController::class);
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{product}', [ProductController::class, 'update']);
    Route::delete('/product/{product}', [ProductController::class, 'destroy']);
    Route::get('/clients', [ClientController::class, 'GetClientsData']);
    Route::get('/subscriptions', [ClientController::class, 'GetSubsciptionStatic']);
    Route::get('/AdminInformations', [AdminActionsController::class, 'GetAdminInfo']);
    Route::put('/UpdateAdminAccount/{id}', [AdminActionsController::class, 'UpdateAdminAccount']);
    Route::apiResource('/offres', SpecialOfferController::class);
    Route::apiResource('/employees', EmployeeController::class);
    Route::patch('/UpdateStatusReview/{id}', [ReviewController::class, 'ChangeReviewStatus']);

    Route::prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index']);               
    Route::get('/top-categories', [SalesController::class, 'topCategories']);  
    Route::get('/top-types', [SalesController::class, 'topTypes']);         
    Route::get('/by-product', [SalesController::class, 'salesByProduct']);  
    Route::get('/by-filters', [SalesController::class, 'filter_by_times']);
    Route::get('/sales_statistic', [SalesController::class, 'sales_statistic']);
    });
    Route::post('/add_manager', [AdminActionsController::class, 'Add_Manager']);
    Route::get('/managers', [AdminActionsController::class, 'GetAllManagers']);
    Route::delete('/managers/{id}', [AdminActionsController::class, 'DeleteManager']);
});

Route::middleware(['auth:sanctum', 'manager'])->group(function (){
    Route::apiResource('/type', TypeController::class);
    Route::get('/orders', [OrderController::class, 'GetAllOrders']);
    Route::get('/order/{id}', [OrderController::class, 'GetOrderById']);
    Route::patch('/order/{id}', [OrderController::class, 'UpdateOrderStatus']);
    Route::patch('/product/{id}', [ProductController::class, 'UpdateProductStatus']);
    Route::patch('/UpdateStatusType/{id}', [TypeController::class, 'updateTypeStatus']);

    Route::get('/badges_statistics',[AdminActionsController::class, 'badges_statistics']);
    Route::get('/notifications',[NotificationController::class, 'index']);
  Route::get('/new-orders', function () {
    if (Cache::get('has_new_order')) {
        return response()->json(['hasNewOrder' => true]);
    }

    $lastCheck = Cache::get('last_check_time', Carbon::now('UTC')->subSeconds(10));
    $newOrderExists = Order::where('created_at', '>=', $lastCheck)->exists();

    if ($newOrderExists) {
        Cache::put('has_new_order', true, now()->addSeconds(10));
    }

    Cache::put('last_check_time', Carbon::now('UTC'));

    return response()->json(['hasNewOrder' => $newOrderExists]);
});
});




Route::middleware('auth:sanctum')->group(function (){
    Route::post('/favorite/{productId}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
});


Route::post('/review', [ReviewController::class, 'store'])->middleware('auth:sanctum');
Route::get('/reviews/{productId}', [ReviewController::class, 'index']);
Route::get('/getallreviews', [ReviewController::class, 'GetAllreviews']);


Route::get('/product', [ProductController::class,'index']);
Route::get('/categorie', [CategorieController::class,'index']);

Route::get('/userOrders',[OrderController::class,'GetUserOrders'])->middleware('auth:sanctum');


Route::post('/order', [OrderController::class, 'store']);


Route::get('/reviews/{productId}', [ReviewController::class, 'Getreviewsbyid']);
