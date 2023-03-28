<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MercadoPagoAccounts extends Model
{
    use SoftDeletes;
	
    protected $guarded = [];
    protected $table = 'mercado_pago_accounts';
    public $timestamps = true;
}
