<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loans';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'principal',
        'installment',
        'loan_start_date',
        'loan_end_date',
        'period',
        'interest'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'loan_id');
    }
}
