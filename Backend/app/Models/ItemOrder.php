<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'order_status', 'order_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemOrderDetails()
    {
        return $this->hasMany(ItemOrderDetail::class);
    }
}
