<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shops extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = [];
    protected $table = 'shops';
    public $timestamps = true;

    public function getFDocumentAttribute()
    {
        return (strlen($this->document) == 11) ? $this->mask('###.###.###-##', $this->document) : $this->mask('##.###.###/####-##', $this->document);
    }

    public function getFPhoneAttribute()
    {
        return (strlen($this->phone) == 11) ? $this->mask('(##) #####-####', $this->phone) : $this->mask('(##) ####-####', $this->phone);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Suppliers::class, 'shop_suppliers', 'shop_id', 'supplier_id')->withPivot('date');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'shop_products', 'shop_id', 'product_id')->withPivot('date', 'exported');
    }

    public function supplier_products($supplier_id)
    {
        return $this->products->where('supplier_id', $supplier_id);
    }

    public function shopify_app()
    {
        return $this->hasOne(ShopifyApps::class, 'shop_id');
    }

    public function woocommerce_app()
    {
        return $this->hasOne(WoocommerceApps::class, 'shop_id');
    }

    public function cartx_app()
    {
        return $this->hasOne(CartxApps::class, 'shop_id');
    }

    public function yampi_app()
    {

        return $this->hasOne(YampiApps::class, 'shop_id');

    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'shop_id');
    }

    public function address()
    {
        return $this->hasOne(ShopAddress::class, 'shop_id');
    }

    public function contracted_plan()
    {
        return $this->hasOne(ShopContractedPlans::class, 'shop_id');
    }

    public function internal_subscription()
    {
        return $this->hasOne(InternalSubscriptionShop::class, 'shop_id');
    }

    public function canceled_plan()
    {
        return $this->hasOne(ShopCanceledPlans::class, 'shop_id');
    }

    public function token_card()
    {
        return $this->hasOne(TokenCardShop::class, 'shop_id');
    }

    public function shopify_webhooks()
    {
        return $this->hasMany(ShopShopifyWebhooks::class, 'shop_id');
    }

    public function woocommerce_webhooks()
    {
        return $this->hasMany(ShopWoocommerceWebhooks::class, 'shop_id');
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function getImgProfileAttribute($value)
    {
        if (strpos($value, ';base64,') !== false) {
            return 'data:image/jpeg;base64,' . $value;
        }
    }

    function mask($mask, $str)
    {

        $str = str_replace(" ", "", $str);

        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "#")] = $str[$i];
        }

        return $mask;

    }
}
