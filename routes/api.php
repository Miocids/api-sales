<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController,
    UserController,
    CustomerController,
    ItemController,
    NoteController,
    NoteItemController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix("/auth/")->group(function (){
    Route::controller(AuthController::class)->group(function (){
        Route::post("sign-in","signIn")->name("sign-in");
        Route::post("sign-up","signUp")->name("sign-up");
        Route::post("forgot-password","forgot")->name("forgot");
        Route::post("reset-password","reset")->name("reset");
    });
});

Route::middleware(['auth:api'])->group( function () {
    Route::prefix("/auth/")->group(function (){
        Route::controller(AuthController::class)->group(function (){
            Route::get("logout","logout")->name("logout");
            Route::post("authorization","authorization")->name("authorization");
        });
    });
    Route::apiResource("users",UserController::class)->parameters(["users" => "id"]);
    Route::apiResource("customers",CustomerController::class)->parameters(["customers" => "id"]);
    Route::apiResource("items",ItemController::class)->parameters(["items" => "id"]);
    Route::apiResource("notes",NoteController::class)->parameters(["notes" => "id"]);
    Route::apiResource("note-items",NoteItemController::class)->parameters(["note-items" => "id"]);

});