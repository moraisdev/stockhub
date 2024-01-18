<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Products;

class Suppliers extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = [];
    protected $table = 'suppliers';
    public $timestamps = true;

    public function correios_settings(){
        return $this->hasOne(CorreiosSettings::class, 'supplier_id');
    }

    public function total_express_settings(){
        return $this->hasOne(TotalExpressSettings::class, 'supplier_id');
    }

    public function melhor_envio_settings(){
        return $this->hasOne(MelhorEnvioSettings::class, 'supplier_id');
    }

    public function shops(){
    	return $this->belongsToMany(Shops::class, 'shop_suppliers', 'supplier_id', 'shop_id')->withPivot('date');
    }

    public function products(){
        return $this->hasMany(Products::class, 'supplier_id');
    }    

    public function mp_account(){
        return $this->hasOne(MercadoPagoAccounts::class, 'supplier_id');
    }

    public function gateways(){
        return $this->belongsToMany(PaymentGateways::class, 'supplier_gateways', 'supplier_id', 'gateway_id');
    }

    public function address(){
        return $this->hasOne(SupplierAddress::class, 'supplier_id')->where('type', 'default');
    }

    public function shipment_address(){
        return $this->hasOne(SupplierAddress::class, 'supplier_id')->where('type', 'shipment');
    }

    public function bank(){
        return $this->hasOne(SupplierBank::class, 'supplier_id');
    }
    
    public function contracted_plan(){
        return $this->hasOne(SupplierContractedPlans::class, 'supplier_id');
    }

    public function canceled_plan(){
        return $this->hasOne(SupplierCanceledPlans::class, 'supplier_id');
    }
}
