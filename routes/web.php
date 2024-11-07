<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::resource('restaurants', Admin\RestaurantController::class);
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('company', Admin\CompanyController::class);
    Route::resource('terms', Admin\TermController::class);
});

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('restaurants', RestaurantController::class)->only(['index','show']);
    Route::get('company', [CompanyController::class, 'index'])->name('company.index');
    Route::get('terms', [TermController::class, 'index'])->name('terms.index');

    Route::group(['middleware' => ['auth','verified','notsubscribed'],'prefix' => 'subscription', 'as' => 'subscription.'], function () {
        Route::get('create', [SubscriptionController::class, 'create'])->name('create');
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        
    });
    Route::group(['middleware' => ['auth','verified','subscribed'],'prefix' => 'subscription', 'as' => 'subscription.'], function () {
        
        Route::get('edit', [SubscriptionController::class, 'edit'])->name('edit');
        Route::patch('update', [SubscriptionController::class, 'update'])->name('update');
        Route::get('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::delete('/', [SubscriptionController::class, 'destroy'])->name('destroy');
        Route::resource('restaurants.reviews',ReviewController::class)->only(['create','store','edit','update','destroy']);
    });
    Route::group(['middleware' => ['auth','verified','subscribed']], function () {
    Route::resource('restaurants.reviews',ReviewController::class)->only(['create','store','edit','update','destroy']);
    Route::resource('restaurants.reservations',ReservationController::class)->only(['create','store']);
    Route::resource('reservations',ReservationController::class)->only(['index','destroy']);
    Route::get('favorites',[FavoriteController::class,'index'])->name('favorites.index');
        Route::post('favorites/{restaurant}',[FavoriteController::class,'store'])->name('favorites.store');
        Route::delete('favorites/{restaurant}',[FavoriteController::class,'destroy'])->name('favorites.destroy');
    });
    Route::group(['middleware' => ['auth','verified']], function () {
        Route::resource('user',UserController::class)->only(['index','edit','update']);
        Route::get('/restaurants/{restaurant}/reviews',[ReviewController::class, 'index'])->name('restaurants.reviews.index');
    });
});
