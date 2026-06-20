<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Loan;
use App\Jobs\BookLoanJob;
use App\Jobs\LoanHistoryJob;

class LoanController extends Controller
{
    private function hasuraUrl(): string
    {
        return env('HASURA_URL', 'http://hasura:8080') . '/v1/graphql';
    }

    private function hasuraHeaders(): array
    {
        return [
            'X-Hasura-Admin-Secret' => env('HASURA_ADMIN_SECRET', 'myadminsecretkey'),
        ];
    }

    public function index()
    {
        return response()->json(Loan::all());
    }

    public function show($id)
    {
        $loan = Loan::find($id);
        if (!$loan) {
            return response()->json(['message' => 'Loan tidak ditemukan'], 404);
        }
        return response()->json($loan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'book_id' => 'required'
        ]);

        $userId = $request->user_id;
        $bookId = $request->book_id;

        // CEK USER (REST to user-service - kept for backward compat)
        $userResponse = Http::get("http://user-service:8000/api/users/$userId");
        if ($userResponse->failed()) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // CEK BOOK via Hasura GraphQL
        $bookResponse = Http::withHeaders($this->hasuraHeaders())->post($this->hasuraUrl(), [
            'query' => 'query ($id: bigint!) { books_by_pk(id: $id) { id judul penulis stok } }',
            'variables' => ['id' => (int) $bookId],
        ]);

        $bookData = $bookResponse->json('data.books_by_pk');

        if (!$bookData) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        if ($bookData['stok'] <= 0) {
            return response()->json(['message' => 'Stok tidak tersedia'], 400);
        }

        // Async — kurangi stok buku (via RabbitMQ, processed by book-worker)
        BookLoanJob::dispatch($bookId)->onQueue('book-loan');

        // SIMPAN LOAN
        $loan = Loan::create([
            'user_id'     => $userId,
            'book_id'     => $bookId,
            'loan_date'   => now(),
            'return_date' => null,
            'status'      => 'dipinjam'
        ]);

        // Async — kirim event riwayat peminjaman ke user-service
        LoanHistoryJob::dispatch(
            $userId,
            $bookId,
            $loan->id,
            'dipinjam',
            $loan->loan_date->toDateTimeString()
        )->onQueue('loan-history');

        return response()->json([
            'message' => 'Peminjaman berhasil',
            'data'    => $loan
        ], 201);
    }

    public function returnBook($id)
    {
        $loan = Loan::find($id);
        if (!$loan) {
            return response()->json(['message' => 'Loan tidak ditemukan'], 404);
        }

        if ($loan->status === 'dikembalikan') {
            return response()->json(['message' => 'Buku sudah dikembalikan'], 400);
        }

        $loan->update([
            'return_date' => now(),
            'status'      => 'dikembalikan'
        ]);

        // Tambah stok buku via Hasura GraphQL mutation
        Http::withHeaders($this->hasuraHeaders())->post($this->hasuraUrl(), [
            'query' => 'mutation ($id: bigint!) { update_books_by_pk(pk_columns: {id: $id}, _inc: {stok: 1}) { id stok } }',
            'variables' => ['id' => (int) $loan->book_id],
        ]);

        // Async — kirim event riwayat pengembalian ke user-service
        LoanHistoryJob::dispatch(
            $loan->user_id,
            $loan->book_id,
            $loan->id,
            'dikembalikan',
            $loan->loan_date->toDateTimeString(),
            now()->toDateTimeString()
        )->onQueue('loan-history');

        return response()->json([
            'message' => 'Buku berhasil dikembalikan',
            'data'    => $loan
        ]);
    }
}