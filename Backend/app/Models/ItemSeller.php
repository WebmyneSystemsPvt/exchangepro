<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSeller extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id', 'user_id'
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    // Accessor for updated_at date
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
