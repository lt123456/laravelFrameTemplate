<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InfoAgenda extends Model
{
    protected $table = 'info_agenda';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
    public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
        public function getDateAttribute()
    {
        return date('Y-m-d', $this->attributes['date']);
    }
        public function getStartimeAttribute()
    {
        return date('H:i:s', $this->attributes['startime']);
    }
      public function getEndtimeAttribute()
    {
        return date('H:i:s', $this->attributes['endtime']);
    }
        public function info()
    {
        return $this->hasOne('App\Http\Models\Info','id','hid');
    }
}
