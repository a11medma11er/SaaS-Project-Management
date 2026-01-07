<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

// Management Routes (Users, Roles, Permissions)
Route::middleware(['auth'])->prefix('management')->name('management.')->group(function () {
    
    // Users Management
    Route::middleware('can:view-users')->group(function () {
        Route::resource('users', App\Http\Controllers\Management\UserManagementController::class);
    });
    
    // Roles Management
    Route::middleware('can:view-roles')->group(function () {
        Route::resource('roles', App\Http\Controllers\Management\RoleController::class);
    });
    
    // Permissions Management
    Route::middleware('can:view-permissions')->group(function () {
        Route::resource('permissions', App\Http\Controllers\Management\PermissionController::class);
    });
    
    // Projects Management
    Route::middleware('can:view-projects')->group(function () {
        // Toggle favorite BEFORE resource to avoid route conflict
        Route::post('projects/{project}/toggle-favorite', [App\Http\Controllers\Management\ProjectController::class, 'toggleFavorite'])
            ->name('projects.toggleFavorite');
        
        Route::resource('projects', App\Http\Controllers\Management\ProjectController::class);
    });
});

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
