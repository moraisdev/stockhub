<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrders extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'supplier_orders';
    public $timestamps = true;

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

    public function getFDisplayIdAttribute(){
        return 'F'.$this->display_id;
    }

    public function items(){
    	return $this->hasMany(SupplierOrderItems::class, 'supplier_order_id');
    }

    public function supplier(){
    	return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id')->with(['shop', 'customer']);
    }

    public function receipts(){
        return $this->belongsToMany(Receipts::class, 'receipt_orders', 'supplier_order_id', 'receipt_id');
    }

    public function order_receipt(){
        return $this->receipts->where('type', 'order')->first();
    }

    public function shipping_receipt(){
        return $this->receipts->where('type', 'shipping')->first();
    }

    public function shipping(){
        return $this->hasOne(SupplierOrderShippings::class, 'supplier_order_id');
    }

    public function frete_melhor_envio(){
        return $this->hasOne(FreteMelhorEnvio::class, 'supplier_order_id');
    }
}
