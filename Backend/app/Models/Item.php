<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
    protected $guard_name = 'web';

    /**
     * Accessor for created_at date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    /**
     * Accessor for updated_at date.
     *
     * @param  string  $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    // Accessor for item_photo with storage path
    public function getItemPhotoAttribute($value)
    {
        if ($value) {
            return asset('storage/Item' . $value);
        } else {
            return asset('assets/images/281x250.png'); // Use asset() to get the URL
        }
    }

    /**
     * Define the relationship with the Category model.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define the relationship with the SubCategory model.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Define the relationship with the ItemOrderDetail model.
     */
    public function itemOrders()
    {
        return $this->hasMany(ItemOrderDetail::class);
    }

    /**
     * Define the relationship with the Transaction model.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Define the relationship with the ItemSeller model.
     */
    public function sellers()
    {
        return $this->hasMany(ItemSeller::class);
    }

    /**
     * Define the many-to-many relationship with the ItemStorage model.
     */
    public function itemStorages()
    {
        return $this->belongsToMany(ItemStorage::class, 'item_storage_pivot', 'item_id', 'item_storage_id');
    }
}
