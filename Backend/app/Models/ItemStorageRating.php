<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStorageRating extends Model
{
    use HasFactory;

    protected $table = 'item_storage_review';

    protected $guarded = ['id'];
    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}
