<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'city', 'address', 'pincode', 'location', 'phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
