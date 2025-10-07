<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStoragePhoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_photo', 'item_storage_id'
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
    public function getItemPhotoAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        } else {
            return asset('assets/images/281x250.png'); // Use asset() to get the URL
        }
    }

    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class, 'item_storage_id');
    }
}
