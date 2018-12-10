<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InfoTicket extends Model
{
    protected $table = 'ticket';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
}
