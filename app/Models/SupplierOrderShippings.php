<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOrderShippings extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'supplier_order_shippings';
    public $timestamps = true;

    public function getFStatusAttribute(){
        switch ($this->status) {
            case 'pending':
                return 'Pendente';
                break;

            case 'sent':
                return 'Enviado';
                break;

            case 'completed':
                return 'Entregue';
                break;

            case 'canceled':
                return 'Cancelado';
                break;
        }
    }
}
