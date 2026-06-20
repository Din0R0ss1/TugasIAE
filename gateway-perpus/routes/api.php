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

    // ========== BOOKS (via Hasura GraphQL) ==========
    $hasuraUrl = env('HASURA_URL', 'http://hasura:8080') . '/v1/graphql';
    $hasuraHeaders = [
        'X-Hasura-Admin-Secret' => env('HASURA_ADMIN_SECRET', 'myadminsecretkey'),
    ];

    Route::get('/books', function () use ($hasuraUrl, $hasuraHeaders) {
        $res = Http::withHeaders($hasuraHeaders)->post($hasuraUrl, [
            'query' => '{ books { id judul penulis stok created_at updated_at } }',
        ]);

        return response()->json($res->json('data.books') ?? $res->json(), $res->status());
    });

    Route::post('/books', function (Request $request) use ($hasuraUrl, $hasuraHeaders) {
        $res = Http::withHeaders($hasuraHeaders)->post($hasuraUrl, [
            'query' => 'mutation ($object: books_insert_input!) { insert_books_one(object: $object) { id judul penulis stok created_at updated_at } }',
            'variables' => [
                'object' => $request->only(['judul', 'penulis', 'stok']),
            ],
        ]);

        return response()->json(
            $res->json('data.insert_books_one') ?? $res->json(),
            $res->status()
        );
    });

    Route::put('/books/{id}', function (Request $request, $id) use ($hasuraUrl, $hasuraHeaders) {
        $res = Http::withHeaders($hasuraHeaders)->post($hasuraUrl, [
            'query' => 'mutation ($id: bigint!, $set: books_set_input!) { update_books_by_pk(pk_columns: {id: $id}, _set: $set) { id judul penulis stok created_at updated_at } }',
            'variables' => [
                'id'  => (int) $id,
                'set' => $request->only(['judul', 'penulis', 'stok']),
            ],
        ]);

        return response()->json(
            $res->json('data.update_books_by_pk') ?? $res->json(),
            $res->status()
        );
    });

    // ========== USERS (via User Service GraphQL) ==========
    $userGraphqlUrl = env('USER_SERVICE_URL', 'http://user-service:8000') . '/graphql';

    Route::get('/users', function () use ($userGraphqlUrl) {
        $res = Http::post($userGraphqlUrl, [
            'query' => '{ users { id name email created_at updated_at } }',
        ]);

        return response()->json($res->json('data.users') ?? $res->json(), $res->status());
    });

    Route::post('/users', function (Request $request) use ($userGraphqlUrl, $hasuraHeaders) {
        $res = Http::post($userGraphqlUrl, [
            'query' => 'mutation ($name: String!, $email: String!, $password: String) { createUser(name: $name, email: $email, password: $password) { id name email created_at updated_at } }',
            'variables' => [
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => $request->input('password', '123456'),
            ],
        ]);

        return response()->json(
            $res->json('data.createUser') ?? $res->json(),
            $res->status()
        );
    });

    Route::put('/users/{id}', function (Request $request, $id) use ($userGraphqlUrl) {
        $res = Http::post($userGraphqlUrl, [
            'query' => 'mutation ($id: ID!, $name: String!, $email: String!) { updateUser(id: $id, name: $name, email: $email) { id name email created_at updated_at } }',
            'variables' => [
                'id'    => $id,
                'name'  => $request->input('name'),
                'email' => $request->input('email'),
            ],
        ]);

        return response()->json(
            $res->json('data.updateUser') ?? $res->json(),
            $res->status()
        );
    });

    Route::get('/users/{id}/history', function ($id) use ($userGraphqlUrl) {
        $res = Http::post($userGraphqlUrl, [
            'query' => 'query ($id: ID!) { user(id: $id) { id name email loan_histories { id book_id loan_id action loan_date return_date created_at } } }',
            'variables' => ['id' => $id],
        ]);

        $user = $res->json('data.user');

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json([
            'user'    => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
            ],
            'history' => $user['loan_histories'] ?? [],
        ]);
    });

    // ========== LOANS (via REST API) ==========
    Route::get('/loans', function () {
        $res = Http::get('http://loan-service:8000/api/loans');
        return response()->json($res->json(), $res->status());
    });

    Route::post('/loans', function (Request $request) {
        $res = Http::post(
            'http://loan-service:8000/api/loans',
            $request->all()
        );

        return response()->json($res->json(), $res->status());
    });

    Route::put('/loans/{id}/return', function ($id) {
        $res = Http::put(
            "http://loan-service:8000/api/loans/{$id}/return"
        );

        return response()->json($res->json(), $res->status());
    });
});
