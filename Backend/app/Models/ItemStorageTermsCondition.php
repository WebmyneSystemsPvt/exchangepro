<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStorageTermsCondition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_storage_id', 'title', 'description'
    ];

    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class, 'item_storage_id');
    }
}
