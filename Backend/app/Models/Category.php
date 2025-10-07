<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    protected $table = 'categories';

    protected $guard_name = 'web';

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

    // Relationship with SubCategory
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'categories_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function itemStorages()
    {
        return $this->hasMany(ItemStorage::class, 'categories_id');
    }


}
