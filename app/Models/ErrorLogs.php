<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLogs extends Model
{
    protected $guarded = [];
    protected $table = 'error_logs';
    public $timestamps = true;
}
