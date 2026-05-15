<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Loan;
use App\Jobs\BookLoanJob;
use App\Jobs\LoanHistoryJob;

class LoanController extends Controller
{
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

        // 🔥 CEK USER
        $userResponse = Http::get("http://user-service:8000/api/users/$userId");
        if ($userResponse->failed()) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // 🔥 CEK BOOK
        $bookResponse = Http::get("http://book-service:8000/api/books/$bookId");
        if ($bookResponse->failed()) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $book = $bookResponse->json();

        // 🔥 VALIDASI STOK
        if (!isset($book['stok'])) {
            return response()->json([
                'message' => 'Format data buku salah',
                'debug'   => $book
            ], 500);
        }

        if ($book['stok'] <= 0) {
            return response()->json(['message' => 'Stok tidak tersedia'], 400);
        }

        // ✅ Async 1 — kurangi stok buku
        BookLoanJob::dispatch($bookId)->onQueue('book-loan');

        // ✅ SIMPAN LOAN
        $loan = Loan::create([
            'user_id'     => $userId,
            'book_id'     => $bookId,
            'loan_date'   => now(),
            'return_date' => null,
            'status'      => 'dipinjam'
        ]);

        // ✅ Async 2 — kirim event riwayat peminjaman ke user-service
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

        // ✅ Tambah stok buku kembali (sync HTTP)
        Http::put("http://book-service:8000/api/books/$loan->book_id/add-stock");

        // ✅ Async — kirim event riwayat pengembalian ke user-service
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