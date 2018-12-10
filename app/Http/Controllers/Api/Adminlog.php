<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Models\AdminLogs as log;
use App\Http\Models\AdminAuth;
use App\Http\Models\AuthRule;
use App\Http\Models\AuthGroup;
//管理员日志
class Adminlog extends Api
{		

	/**
	 * 列表
	 * [list description]
	 * @author XD
	 * @Date   2018-10-30
	 * @return [type]     [description]
	 */
	public function list(Request $request){
		//分页
    	$offset                = ($request['page']-1)*$request['limit'];
    	$admin                 = new log();
    	$list                  = $admin
    							->where(function ($query) use ($request) {
								    $request['admin_name'] && $query->where('admin_name',  'like' , '%'.$request['admin_name'].'%');
								})
								->orderBy('addtime','desc')
    							->offset($offset)
    							->limit($request['limit'])
    							->get()
    							->toArray();
    	//数据总条数			
    	$count                   = $admin
    							->where(function ($query) use ($request) {
								    $request['admin_name'] && $query->where('admin_name',  'like' , '%'.$request['admin_name'].'%');
								})
    							->count();
    	return $this->success($list,$count);
	}
}
