<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $table = 'people';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
    public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
    public function sign()
    {
        return $this->hasOne('App\Http\Models\PeopleSign','pid');
    }
     public function type()
    {
        return $this->hasOne('App\Http\Models\PeopleType','id','typeid');
    }
     public function ticket()
    {
        return $this->hasOne('App\Http\Models\InfoTicket','id','tid');
    }
        public function info()
    {
        return $this->hasOne('App\Http\Models\Info','id','hid');
    }
}
