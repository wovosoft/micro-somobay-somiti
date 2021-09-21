<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("login");
});


Route::middleware(['auth', 'verified'])
    ->prefix("/admin")
    ->group(function () {
        Route::post("/members/find/{member}", [MemberController::class, 'find']);
        Route::post("/members/profile/{member}", [MemberController::class, 'profile']);
        Route::post("/members/search", [MemberController::class, 'search']);
        Route::post("/members/store", [MemberController::class, 'store']);
        Route::post("/members/destroy", [MemberController::class, 'destroy']);
        Route::post("/members/export/balance_sheet", [MemberController::class, 'exportbalanceSheet']);
        Route::post("/members", [MemberController::class, 'index']);

        Route::post("/expenses/destroy", [ExpenseController::class, 'destroy']);
        Route::post("/expenses/store", [ExpenseController::class, 'store']);
        Route::post("/expenses/export", [ExpenseController::class, 'export']);
        Route::post("/expenses", [ExpenseController::class, 'index']);

        Route::post("/deposits/destroy", [DepositController::class, 'destroy']);
        Route::post("/deposits/store", [DepositController::class, 'store']);
        Route::post("/deposits", [DepositController::class, 'index']);
        Route::post("/deposits/export", [DepositController::class, 'export']);

        Route::post("/withdraws/destroy", [WithdrawController::class, 'destroy']);
        Route::post("/withdraws/store", [WithdrawController::class, 'store']);
        Route::post("/withdraws", [WithdrawController::class, 'index']);
        Route::post("/withdraws/export", [WithdrawController::class, 'export']);

        Route::post("/users/destroy", [UserController::class, 'destroy']);
        Route::post("/users/store", [UserController::class, 'store']);
        Route::post("/users", [UserController::class, 'index']);
    });

Route
    ::get('/admin/{any?}', function () {
        return view("admin");
    })
    ->where('any', '.*')
    ->middleware(['auth', 'verified'])
    ->name('admin');


require __DIR__ . '/auth.php';
