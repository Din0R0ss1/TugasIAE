<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanHistory extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'loan_id',
        'action',
        'loan_date',
        'return_date',
    ];

    protected $casts = [
        'loan_date'   => 'datetime',
        'return_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}