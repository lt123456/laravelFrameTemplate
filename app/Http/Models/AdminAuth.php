<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuth extends Model
{
    protected $table = 'admin_auth';
    public $timestamps = false;
    /**
     * 权限一对一
     * [auth description]
     * @author XD
     * @Date   2018-10-12
     * @return [type]     [description]
     */
      public function authgroup()
    {
        return $this->hasOne('App\Http\Models\AuthGroup','id','auth_id');
    }
}
