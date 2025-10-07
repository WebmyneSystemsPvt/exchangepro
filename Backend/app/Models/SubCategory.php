<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class SubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_categories';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

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
    public function getPhotoAttribute($value)
    {
        return asset('storage/' . $value);
    }

    // Relationship with Category
    public function getCategory()
    {
        return $this->belongsTo(Category::class,'categories_id','id');
    }

    public function itemStorages()
    {
        return $this->hasMany(ItemStorage::class, 'sub_categories_id');
    }
}
