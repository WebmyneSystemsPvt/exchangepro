<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupsPivotes extends Model
{
    use HasFactory;
    protected $table = 'group_pivote';

    protected $guarded = ['id'];

    protected $guard_name = 'web'; //

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
    /**
     * Get the group that owns the pivot.
     */
    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id', 'id');
    }

    /**
     * Get the seller that owns the pivot.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    /**
     * Get the document that owns the pivot.
     */
    public function document()
    {
        return $this->belongsTo(GroupsDocuments::class, 'document_id', 'id');
    }
}
