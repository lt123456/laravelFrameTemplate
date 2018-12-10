<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InfoGuest extends Model
{
    protected $table = 'info_guest';
    public $timestamps = false;
       public function guest()
    {
        return $this->hasOne('App\Http\Models\Guest','id','gid');
    }
           public function info()
    {
        return $this->hasOne('App\Http\Models\Info','id','hid');
    }
}
