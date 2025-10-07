<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStorageBookingStatus extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'item_storage_booking_status';

    // Define the attributes that are mass assignable
    protected $fillable = [
        'item_storage_id',
        'borrower_id',
        'start_date',
        'end_date',
        'booking_status',
    ];

    // Define any relationships if needed
    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class, 'item_storage_id');
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }
}
