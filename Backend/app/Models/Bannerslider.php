<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bannerslider extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'banner_slider';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    protected $guard_name = 'web';

}
