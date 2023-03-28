<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateLink extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function registered_users()
    {
        return $this->hasMany(IndicationShop::class, 'affiliate_links_id');
    }
    
    public function accesses()
    {
        return $this->hasMany(AccessesAffiliateLink::class, 'affiliate_links_id');
    }
}
