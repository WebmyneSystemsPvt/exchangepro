<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'city', 'address', 'pincode', 'location', 'phone_number', 'availabilityMF', 'availabilitySS'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
