<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}