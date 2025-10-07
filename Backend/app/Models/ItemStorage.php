<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStorage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'listing_type', 'categories_id', 'sub_categories_id', 'location','user_id','map_pin','exception_details',
        'rate', 'rented_max_allow_days','description','default_storage_photo',
        'country','state','city','pincode','landmark','latitude','longitude','status'
    ];

    // Accessor for created_at date
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    // Accessor for updated_at date
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    // Accessor for item_photo with storage path
    public function getDefaultStoragePhotoAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        } else {
            return asset('assets/images/281x250.png'); // Use asset() to get the URL
        }
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_categories_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_storage_pivot', 'item_storage_id', 'item_id');
    }

    public function photos()
    {
        return $this->hasMany(ItemStoragePhoto::class, 'item_storage_id');
    }

    public function sellers(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function facilityOffers()
    {
        return $this->hasMany(ItemStorageFacilityOffer::class, 'item_storage_id');
    }

    public function termsConditions()
    {
        return $this->hasMany(ItemStorageTermsCondition::class, 'item_storage_id');
    }

    public function tags()
    {
        return $this->hasMany(ItemStorageTag::class,'item_storage_id');
    }
    public function ratings()
    {
        return $this->hasMany(ItemStorageRating::class,'item_storage_id');
    }

    public function itemStorageBlockDays()
    {
        return $this->hasMany(ItemStorageBlockDays::class,'item_storage_id');
    }

    public function bookingStatus()
    {
        return $this->hasMany(ItemStorageBookingStatus::class, 'item_storage_id', 'id');
    }
}
