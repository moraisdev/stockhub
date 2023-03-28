<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'categories';
    public $timestamps = true;

    public function products(){
        return $this->hasMany(Products::class, 'category_id');
    }
}
