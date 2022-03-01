<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profiles';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'profileable_id',
        'profileable_type',
        'identity_number',
        'full_name',
        'nick_name',
        'gender',
        'nationality',
        'address',
        'post_code',
        'phone'
    ];

    public function profileable()
    {
        return $this->morphTo();
    }
}
