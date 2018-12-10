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
class Auth extends Api
{	
	
	/**
	 * 权限管理
	 * [Menu description]
	 * @author XD
	 * @Date   2018-10-12
	 */
	public function Menu(){
		//用户信息
		$user = self::$user;
		//权限组
		if($user['rules'] == '*'){
			$res = AuthRule::where(['status'=>1])
							->where(['ismenu'=>1])
							->orderBy('weight')
							->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
							->toArray();
		}else{
			$res = AuthRule::where(['status'=>1])
			->where(['ismenu'=>1])
			->whereIn('id',explode(',',$user['rules']))
			->orderBy('weight')
			->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
			->toArray();
		}
		//处理成树形结构
		$res = $this->genTree($res);
		return $this->success($res);
	}

	/**
	 * 角色列表
	 * [rule description]
	 * @author XD
	 * @Date   2018-10-16
	 * @return [type]     [description]
	 */
	public function rule(){
		$res = AuthGroup::where('status','<>',3)->get(['id','name','pid','remark','status'])->toArray();
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
	 * 角色权限
	 * [groupmenu description]
	 * @author XD
	 * @Date   2018-11-01
	 * @return [type]     [description]
	 */
	public function groupmenu(Request $request){
		//编辑时候
		if($request['id'] != 'undefined'){
			$role = AuthGroup::where('id',$request['id'])->first();

			if(!isset($request['rule_id'])){
				//顶级权限
					if($role->pid == 0){
						$res = AuthRule::where(['status'=>1])
									->orderBy('weight')
									->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
									->toArray();
					}else{

						$prole = AuthGroup::where('id',$role->pid)->first();
						if($prole->rules == '*'){
							$res = AuthRule::where(['status'=>1])
									->orderBy('weight')
									->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
									->toArray();
							}else{
								$res = AuthRule::where(['status'=>1])
								->whereIn('id',explode(',', $prole->rules))
								->orderBy('weight')
								->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
								->toArray();
							}
					
					}
					
			}else{
				$prole = AuthGroup::where('id',$request['rule_id'])->first();
				if($request['rule_id'] == 0){
					$res = AuthRule::where(['status'=>1])
								->orderBy('weight')
								->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
								->toArray();
				}else{

					if($prole->rules == '*'){
						$res = AuthRule::where(['status'=>1])
								->orderBy('weight')
								->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
								->toArray();
						}else{
							$res = AuthRule::where(['status'=>1])
							->whereIn('id',explode(',', $prole->rules))
							->orderBy('weight')
							->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
							->toArray();
					}
				}
			}
			$arr['checked'] = true;
			//角色拥有权限
			if($role->rules == '*'){
				array_walk($res, function (&$value, $key, $arr) {
                            $value = array_merge($value, $arr);
                        }, $arr);
			}else{
				$rules = explode(',', $role->rules);
				foreach ($res as $key => $value) {
					if(in_array($value['id'],$rules)){
						$res[$key]['checked'] = true; 
					}
				}
			}

		}else{
			//新增
			if(!$request['rule_id'] || $request['rule_id'] == 0){
				//所有权限
				$res = AuthRule::where(['status'=>1])
							->orderBy('weight')
							->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
							->toArray();
			}else{
				$prole = AuthGroup::where('id',$request['rule_id'])->first();
				if($prole->rules == '*'){
					$res = AuthRule::where(['status'=>1])
							->orderBy('weight')
							->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
							->toArray();
					}else{
						$res = AuthRule::where(['status'=>1])
						->whereIn('id',explode(',', $prole->rules))
						->orderBy('weight')
						->get(['id','name as title','url as jump','icon','condition','remark','ismenu','pid','weight','status'])
						->toArray();
				}
			}
		}
		
		//处理成树形结构
		$res = $this->genTree($res);
		return $this->success($res);
	}
	/**
	 * 角色删除
	 * [groupdel description]
	 * @author XD
	 * @Date   2018-11-01
	 * @return [type]     [description]
	 */
	public function groupdel(Request $request){
		if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $res = AuthGroup::whereIn('id',$ids)->update(['status'=>3]);
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

	/**
	 * 角色修改
	 * [groupedit description]
	 * @author XD
	 * @Date   2018-11-02
	 * @return [type]     [description]
	 */
	public function groupedit(Request $request){
		$AuthGroup      = new AuthGroup();
		if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
		$data          = $request->all(['rules','name','pid','remark','status']);
		if($data['rules']){
			$data['rules'] = implode(',',$data['rules']);
		}else $data['rules'] = '';
		$res           = $AuthGroup->where('id',$request['ids'])->update($data);
		if($res){
    		return $this->success([]);
    	}else{
    		return $this->fail(600000);
    	}
	}

	/**
	 * 角色新增
	 * [groupadd description]
	 * @author XD
	 * @Date   2018-11-02
	 * @return [type]     [description]
	 */
	public function groupadd(Request $request){
		$AuthGroup      = new AuthGroup();
		if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name' => 'required|string',
	       'remark' => 'required|string',
	    ],[
            'name.required' => '角色名不能为空',
            'remark.required' => '备注不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
		$data          = $request->all(['rules','name','pid','remark','status']);
		if($data['rules']){
			$data['rules'] = implode(',',$data['rules']);
		}else $data['rules'] = '';
		$res           = $AuthGroup->insert($data);
    	if(!$res){
    		 return $this->fail(600000);
    	}else{
    		return $this->success();
    	}
	}

}
