<?php

use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/check-payroll', function (Request $request) {
    $date = Carbon::parse($request->date);
    $exists = Payroll::whereMonth('payroll_date', $date->month)
        ->whereYear('payroll_date', $date->year)
        ->exists();
    return response()->json(['exists' => $exists]);
});
