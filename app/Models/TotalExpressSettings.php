<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TotalExpressSettings extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'total_express_settings';
    public $timestamps = true;
}
