<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discounts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'discounts';
    public $timestamps = true;

    public function variant(){
        return $this->belongsTo(ProductVariants::class, 'variant_id');
    }
}
