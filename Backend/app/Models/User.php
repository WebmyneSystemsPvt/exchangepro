<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $guard_name = 'web';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Accessor for avatar with storage path
    public function getAvatarAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        } else {
            return asset('assets/images/avatar.jpg'); // Use asset() to get the URL
        }
    }

    public function borrowerDetails()
    {
        return $this->hasOne(BorrowerDetail::class, 'user_id', 'id');
    }

    public function sellerDetails()
    {
        return $this->hasOne(SellerDetail::class, 'user_id', 'id');
    }

    public function itemOrders()
    {
        return $this->hasMany(ItemOrder::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function items()
    {
        return $this->hasMany(ItemSeller::class);
    }

    public function itemStorage()
    {
        return $this->hasMany(ItemStorage::class, 'user_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(Groups::class, 'created_by', 'id');
    }
    public function activeGroups()
    {
        return $this->hasMany(Groups::class, 'created_by', 'id')->where('status','=', 1);
    }

    public function groupPivots()
    {
        return $this->hasMany(GroupsPivotes::class, 'seller_id', 'id');
    }
}
