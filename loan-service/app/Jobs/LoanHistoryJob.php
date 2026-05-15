<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoanHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $bookId,
        public int $loanId,
        public string $action,   // 'dipinjam' atau 'dikembalikan'
        public string $loanDate,
        public ?string $returnDate = null
    ) {}

    public function handle(): void
    {
        // loan-service hanya dispatch event,
        // user-service (loan-history-worker) yang proses & simpan
    }
}