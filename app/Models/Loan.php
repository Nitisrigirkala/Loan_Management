<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'amount',
        'interest_rate',
        'duration_years',
        'lender_id',
        'borrower_id'
    ];

    // Define inverse relationship to lender (User)
    public function lender()
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    // Define inverse relationship to borrower (User)
    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }
}
