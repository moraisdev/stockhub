<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookCalls extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'webhook_calls';
    public $timestamps = true;
}
