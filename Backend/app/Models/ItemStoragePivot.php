<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStoragePivot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_storage_pivot';

    protected $fillable = [
        'item_storage_id', 'item_id'
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

    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class, 'item_storage_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
