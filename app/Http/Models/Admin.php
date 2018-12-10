<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';

   	public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
       	public function getLogintimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['logintime']);
    }
    //远程一对多关联权限
     public function rule()
    {
        return $this->hasManyThrough(
            'App\Http\Models\AuthGroup',
            'App\Http\Models\AdminAuth',
            'admin_id',
            'id', 
            'id', 
            'auth_id'
        )->where('status',1)->select('name');
    }
}
