<?php

namespace App\Models;

use App\Notifications\VerifyEmailApiNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'email',
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'user_id');
    }

    public function oAuthAccessTokens()
    {
        return $this->hasMany(OAuthAccessToken::class, 'user_id');
    }

    public function sendApiEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailApiNotification());
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
