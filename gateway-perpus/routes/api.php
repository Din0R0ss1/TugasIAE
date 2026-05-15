<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ============================================================
// AUTH — tidak perlu token
// ============================================================
Route::post('/auth/login',    [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// ============================================================
// PROTECTED — semua route di bawah butuh JWT token
// ============================================================
Route::middleware('jwt.verify')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ========== BOOKS ==========
    Route::get('/books', function () {
        $res = Http::get(env('BOOK_SERVICE_URL') . '/api/books');
        return response()->json($res->json(), $res->status());
    });
    Route::post('/books', function (Request $request) {
        $res = Http::post(env('BOOK_SERVICE_URL') . '/api/books', $request->all());
        return response()->json($res->json(), $res->status());
    });
    Route::put('/books/{id}', function (Request $request, $id) {
        $res = Http::put(env('BOOK_SERVICE_URL') . "/api/books/{$id}", $request->all());
        return response()->json($res->json(), $res->status());
    });

    // ========== USERS ==========
    Route::get('/users', function () {
        $res = Http::get(env('USER_SERVICE_URL') . '/api/users');
        return response()->json($res->json(), $res->status());
    });
    Route::post('/users', function (Request $request) {
        $res = Http::post(env('USER_SERVICE_URL') . '/api/users', $request->all());
        return response()->json($res->json(), $res->status());
    });
    Route::put('/users/{id}', function (Request $request, $id) {
        $res = Http::put(env('USER_SERVICE_URL') . "/api/users/{$id}", $request->all());
        return response()->json($res->json(), $res->status());
    });
    // ✅ NEW — Riwayat peminjaman per user
    Route::get('/users/{id}/history', function ($id) {
        $res = Http::get(env('USER_SERVICE_URL') . "/api/users/{$id}/history");
        return response()->json($res->json(), $res->status());
    });

    // ========== LOANS ==========
    Route::get('/loans', function () {
        $res = Http::get(env('LOAN_SERVICE_URL') . '/api/loans');
        return response()->json($res->json(), $res->status());
    });
    Route::post('/loans', function (Request $request) {
        $res = Http::post(env('LOAN_SERVICE_URL') . '/api/loans', $request->all());
        return response()->json($res->json(), $res->status());
    });
    Route::put('/loans/{id}/return', function ($id) {
        $res = Http::put(env('LOAN_SERVICE_URL') . "/api/loans/{$id}/return");
        return response()->json($res->json(), $res->status());
    });
});