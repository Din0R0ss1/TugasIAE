<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LoanHistory;
use Illuminate\Support\Facades\Log;

class LoanHistoryConsumerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $bookId,
        public int $loanId,
        public string $action,
        public string $loanDate,
        public ?string $returnDate = null
    ) {}

    public function handle(): void
    {
        LoanHistory::create([
            'user_id'     => $this->userId,
            'book_id'     => $this->bookId,
            'loan_id'     => $this->loanId,
            'action'      => $this->action,
            'loan_date'   => $this->loanDate,
            'return_date' => $this->returnDate,
        ]);

        Log::info("✅ LoanHistory disimpan", [
            'user_id' => $this->userId,
            'book_id' => $this->bookId,
            'loan_id' => $this->loanId,
            'action'  => $this->action,
        ]);
    }
}