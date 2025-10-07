<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Groups extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'groups';

    protected $dates = ['deleted_at'];
    protected $guard_name = 'web';

    /**
     * Accessor for created_at date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    /**
     * Accessor for updated_at date.
     *
     * @param  string  $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(config('constants.UNIVERSAL_DATE_FORMAT'));
    }

    public function documents()
    {
        return $this->hasMany(GroupsDocuments::class,'group_id','id');
    }
    public function users()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

    /**
     * Get the pivot table records for the group.
     */
    public function groupPivote()
    {
        return $this->hasMany(GroupsPivotes::class, 'group_id', 'id');
    }
}
