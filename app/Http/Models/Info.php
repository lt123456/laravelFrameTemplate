<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $table = 'info';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
      public function type()
    {
        return $this->hasOne('App\Http\Models\InfoType','id','type');
    }
        public function getConsultContentAttribute()
    {
        return json_decode($this->attributes['consult_content'],1);
    }
      public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
     public function getStartimeAttribute()
    {
        return date('Y-m-d', $this->attributes['startime']);
    }
     public function getEndtimeAttribute()
    {
        return date('Y-m-d', $this->attributes['endtime']);
    }
}
