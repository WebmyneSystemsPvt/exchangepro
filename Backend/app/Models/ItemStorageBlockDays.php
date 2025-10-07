<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStorageBlockDays extends Model
{
    use HasFactory;

    protected $table = 'item_storage_block_days';

    protected $guarded = ['id'];
    public function itemStorage()
    {
        return $this->belongsTo(ItemStorage::class);
    }

    public function getBlockDaysDateAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

}
