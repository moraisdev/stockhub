<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admins extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = [];
    protected $table = 'admins';
    public $timestamps = true;
}
