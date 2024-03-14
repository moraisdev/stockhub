<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectiveImport extends Model
{
    protected $guarded = [];
    protected $table = 'collective_import';
    public $timestamps = true;

    public function shop()
    {
        return $this->belongsTo(Shops::class, 'shop_id');
    }
}