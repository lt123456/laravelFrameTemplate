<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model
{
    protected $table = 'auth_group';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
}
