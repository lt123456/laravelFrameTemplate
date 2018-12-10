<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guest';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';

      public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
}
