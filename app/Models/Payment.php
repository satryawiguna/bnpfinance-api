<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'loan_id',
        'payment',
        'balance',
        'payment_date',
        'description'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
