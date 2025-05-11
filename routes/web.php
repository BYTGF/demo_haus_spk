<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InputFinanceController;
use App\Http\Controllers\InputOperationalController;
use App\Http\Controllers\InputStoreController;
use App\Http\Controllers\InputBDController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route buat guest (belum login)

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store']);
});



// Route buat user yang udah login
Route::middleware('auth')->group(function () {
    // Change the home route to something specific
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/completion-data', [DashboardController::class, 'getCompletionData'])
     ->name('dashboard.completion-data');

    Route::prefix('api')->group(function () {
        Route::get('/dashboard/data', [DashboardController::class, 'getChartData']);
    });
    
	Route::resource('user-management', UserManagementController::class)->middleware('role:Admin');

    Route::middleware('role:Finance,Manager Business Development')->group(function () {
        Route::resource('finance', InputFinanceController::class);  
            // Custom workflow routes
        Route::post('finance/{review}/approve', [InputFinanceController::class, 'approve'])
            ->name('finance.approve');
        Route::post('finance/{review}/reject', [InputFinanceController::class, 'reject'])
            ->name('finance.reject');
    });
    
    Route::middleware('role:Operational,Manager Business Development')->group(function () {
        Route::resource('operational', InputOperationalController::class);  
            // Custom workflow routes
        Route::post('operational/{input}/approve', [InputOperationalController::class, 'approve'])
            ->name('operational.approve');
        Route::post('operational/{input}/reject', [InputOperationalController::class, 'reject'])
            ->name('operational.reject');
    });

    Route::middleware('role:Business Development Staff,Manager Business Development')->group(function () {
        Route::resource('bd', InputBDController::class);  
            // Custom workflow routes
        Route::post('bd/{input}/approve', [InputBDController::class, 'approve'])
            ->name('bd.approve');
        Route::post('bd/{input}/reject', [InputBDController::class, 'reject'])
            ->name('bd.reject');
    });

    Route::middleware('role:Area Manager,Store Manager,Manager Business Development')->group(function () {
        Route::resource('store', InputStoreController::class);  
            // Custom workflow routes
        Route::post('store/{input}/approve-area', [InputStoreController::class, 'approveArea'])
        ->name('store.approve-area');
        Route::post('store/{input}/approve-bd', [InputStoreController::class, 'approveBd'])
            ->name('store.approve-bd');
        Route::post('store/{input}/reject', [InputStoreController::class, 'reject'])
            ->name('store.reject');
    });

    Route::get('/logout', [SessionsController::class, 'destroy']);
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
});



// Route::group(['middleware' => 'auth'], function () {

//     Route::get('/', [HomeController::class, 'home']);

// 	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role:Manager');

// 	// Route::get('billing', function () {
// 	// 	return view('billing');
// 	// })->name('billing');

// 	// Route::get('profile', function () {
// 	// 	return view('profile');
// 	// })->name('profile');

// 	// Route::get('rtl', function () {
// 	// 	return view('rtl');
// 	// })->name('rtl');

// 	// Route::get('user-management', function () {
// 	// 	return view('laravel-examples/user-management');
// 	// })->name('user-management');

// 	// Route::get('tables', function () {
// 	// 	return view('tables');
// 	// })->name('tables');

//     // Route::get('virtual-reality', function () {
// 	// 	return view('virtual-reality');
// 	// })->name('virtual-reality');

//     // Route::get('static-sign-in', function () {
// 	// 	return view('static-sign-in');
// 	// })->name('sign-in');

//     // Route::get('static-sign-up', function () {
// 	// 	return view('static-sign-up');
// 	// })->name('sign-up');

//     Route::get('/logout', [SessionsController::class, 'destroy']);
// 	Route::get('/user-profile', [InfoUserController::class, 'create']);
// 	Route::post('/user-profile', [InfoUserController::class, 'store']);
//     Route::get('/login', function () {
// 		return view('dashboard');
// 	})->name('sign-up');
// });



// Route::group(['middleware' => 'guest'], function () {
//     // Route::get('/register', [RegisterController::class, 'create']);
//     // Route::post('/register', [RegisterController::class, 'store']);
//     Route::get('/login', [SessionsController::class, 'create']);
//     Route::post('/session', [SessionsController::class, 'store']);
// 	// Route::get('/login/forgot-password', [ResetController::class, 'create']);
// 	// Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
// 	// Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
// 	// Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

// });

// Route::get('/login', function () {
//     return view('session/login-session');
// })->name('login');