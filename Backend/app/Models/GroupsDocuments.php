<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupsDocuments extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'group_documents';

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

    public function getGroupDocumentAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function group()
    {
        return $this->belongsTo(Groups::class,'group_id','id');
    }
}
