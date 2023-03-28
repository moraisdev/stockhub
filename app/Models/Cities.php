<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $guarded = [];
    protected $table = 'cities';
    public $timestamps = false;

    public function state(){
        return $this->belongsTo(States::class, 'state_id');
    }
}
