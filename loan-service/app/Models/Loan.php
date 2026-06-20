<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['user_id', 'book_id', 'status', 'loan_date', 'return_date',];

    protected $casts = [
        'loan_date'   => 'datetime',
        'return_date' => 'datetime',
    ];
}

