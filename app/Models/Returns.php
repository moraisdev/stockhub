<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Returns extends Model
{
    use SoftDeletes;

    protected $table = 'returns';
    protected $guarded = [];

    public function getFStatusAttribute(){
        switch ($this->status){
            case 'pending':
                return 'Pendente';
                break;
            case 'resolved':
                return 'Resolvido';
                break;
            case 'canceled':
                return 'Cancelado';
                break;
        }
    }

    public function supplier_order(){
        return $this->belongsTo(SupplierOrders::class, 'supplier_order_id');
    }

    public function messages(){
        return $this->hasMany(ReturnMessages::class, 'return_id');
    }
}
