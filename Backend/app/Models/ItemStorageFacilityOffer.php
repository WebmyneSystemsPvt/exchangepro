<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStorageFacilityOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_storage_id', 'title', 'photo', 'description'
    ];

    public function getPhotoAttribute($value)
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
