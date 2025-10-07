<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'item_id', 'item_name', 'item_price'
    ];

    public function order()
    {
        return $this->belongsTo(ItemOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
