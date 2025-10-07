<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'transaction_id', 'rating', 'comment'
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
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
