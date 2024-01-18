<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchSku extends Model
{
    protected $table = 'match_sku';
    protected $fillable = ['sku', 'sku_secondary'];
    public $timestamps = true;

}
