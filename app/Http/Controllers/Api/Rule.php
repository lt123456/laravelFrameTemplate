<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Models\AdminLog;
use App\Http\Models\AdminAuth;
use App\Http\Models\AuthRule;
use App\Http\Models\AuthGroup;
use Validator;

//菜单管理
class Rule extends Api
{	
	/**
	 * 菜单列表
	 * [list description]
	 * @author XD
	 * @Date   2018-11-02
	 * @return [type]     [description]
	 */
	public function list(){
		$res = AuthRule::where('status','<>',3)->orderBy('weight')->get()->toArray();
		//处理成树形结构
		$res = $this->genTrees($res);
		$pid = array_column($res,'pid');
		foreach($res as $k=>$v){
			if(in_array($v['id'], $pid)){
				$res[$k]['display'] = 1;
			}else{
				$res[$k]['display'] = 0;
			}
		}
		return $this->success($res);
	}

	/**
	 * 菜单删除
	 * [del description]
	 * @author XD
	 * @Date   2018-11-04
	 * @param  Request    $request [description]
	 * @return [type]              [description]
	 */
	public function del(Request $request){
		if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $res = AuthRule::whereIn('id',$ids)->update(['status'=>3]);
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

	/**
	 * 修改
	 * [edit description]
	 * @author XD
	 * @Date   2018-11-04
	 * @return [type]     [description]
	 */
	public function edit(Request $request){
		if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name' => 'required|string',
	       'weight' => 'required|integer',
	       'url' => 'required|string',
	    ],[
            'name.required' => '角色名不能为空',
            'weight.required' => '权重错误',
            'url.required' => '规则不能为空',
        ]);
        if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
		$data          = $request->all(['name','url','pid','remark','ismenu','weight','status']);
		if(!$data['remark']) $data['remark'] = '';
		$res           = AuthRule::where('id',$request['id'])->update($data);
		if($res){
    		return $this->success([]);
    	}else{
    		return $this->fail(600000);
    	}
	}

	/**
	 * 新增
	 * [add description]
	 * @author XD
	 * @Date   2018-11-04
	 */
	public function add(Request $request){
		$AuthRule      = new AuthRule();
		if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name' => 'required|string',
	       'weight' => 'required|integer',
	       'url' => 'required|string',
	    ],[
            'name.required' => '角色名不能为空',
            'weight.required' => '权重错误',
            'url.required' => '规则不能为空',
        ]);
        if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
		$data          = $request->all(['name','url','pid','remark','ismenu','weight','status']);
		if(!$data['remark']) $data['remark'] = '';
		$res           = $AuthRule->insert($data);
    	if(!$res){
    		 return $this->fail(600000);
    	}else{
    		return $this->success();
    	}
	}
}
