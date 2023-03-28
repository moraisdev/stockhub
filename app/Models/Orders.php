<?php

namespace App\Models;

use App\Services\OrdersService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;
    public $table = 'orders';
    protected $guarded = [];
    public $timestamps = true;
    public $service;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->service = new OrdersService($this);
    }

    public function getFStatusAttribute(){
        switch ($this->status) {
            case 'pending':
                return 'Pendente';
                break;

            case 'paid':
                return 'Pago';
                break;

            case 'canceled':
                return 'Cancelado';
                break;
        }
    }

    public function items(){
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function shipping(){
        return $this->hasOne(OrderShippings::class, 'order_id');
    }

    public function shipping_label(){
        return $this->hasOne(ShippingLabel::class, 'id', 'shipping_label_id');
    }

    public function shippings(){
        return $this->hasMany(OrderShippings::class, 'order_id');
    }

    public function shop(){
        return $this->belongsTo(Shops::class, 'shop_id');
    }

    public function customer(){
        return $this->belongsTo(Customers::class, 'customer_id')->with('address');
    }

    public function supplier_order(){
        return $this->hasOne(SupplierOrders::class, 'order_id');
    }

    public function supplier_orders(){
        return $this->hasMany(SupplierOrders::class, 'order_id');
    }

    public function receipts(){
        return $this->belongsToMany(Receipts::class, 'receipt_orders', 'order_id', 'receipt_id');
    }

    public function order_receipt(){
        return $this->receipts->where('type', 'order')->first();
    }

    public function shipping_receipt(){
        return $this->receipts->where('type', 'shipping')->first();
    }

    public function customer_receipt(){
        return $this->receipts->where('to', 'customer')->first();
    }
}
