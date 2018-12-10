<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Http\Request;
use App\Http\Models\Info as InfoModel;
use App\Http\Models\InfoType;
use App\Http\Models\PeopleSign;
use Validator;
//会议管理
class Info extends Api
{	
	/**
	 * 列表
	 * [list description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-22
	 * @return [type]     [description]
	 */
	public function list(Request $request){
		 $user = self::$user;
		if(!isset($user['hid'])){
			 return $this->fail(700000);
		}
		//实例化参会人员模型
		$InfoModel  = new InfoModel();
		$res = $InfoModel
					->with('type')
					->where('id',$user['hid'])
					->where('status','<>',3)
					->where(function ($query) use ($request) {
					    $request['name'] && $query->where('name', 'like' , '%'.$request['name'].'%');
					    })
					->get()
					->toArray();
		return $this->success($res);
	}


	/**
	 * 获取会议类别
	 * [type description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function get_type(){
		$PeopleType = new InfoType();
		$res = $PeopleType->get()->toArray();
		return $this->success($res);
	}

	/**
	 * 添加
	 * [add description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 */
	public function add(Request $request){
		//状态类型
		$request['status'] = $request['status']?1:2;
		$request['is_xcx'] = $request['is_xcx']?1:2;
		$request['is_fx']  = $request['is_fx']?1:2;
		$contacts = [];
		//处理参会咨询
		if(!empty($request['consult_title'])){
				// array('area'=>'媒体合作','name'=>'南  蕊','phone'=>'158-1079-0845','email'=>'nanrui@hmkx.cn'),
				foreach($request['consult_title'] as $k=>$v){
					$contacts[$k]['title']  = $v;
					$contacts[$k]['name']  = $request['consult_name'][$k];
					$contacts[$k]['phone'] = $request['consult_phone'][$k];
					$contacts[$k]['email'] = $request['consult_email'][$k];
				}
				$contacts = json_encode($contacts);
		}
		$request['consult_content'] = $contacts;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name'     => 'required|string',
	       'startime' => 'required|string',
	       'endtime'  => 'required|string',
	       'address'  => 'required|string',
	    ],[
            'name.required'     => '会议名称不能为空',
            'startime.required' => '开始时间不能为空',
            'endtime.required'  => '结束时间不能为空',
            'address.required'  => '地址不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$InfoModel        = new InfoModel();
    	$data             = $request->all(['type','name','content','startime','endtime','scale','money','money_content','address','address_content','consult_content','status','pc_img','wap_img','is_xcx','is_fx']);
    	$data['startime'] = strtotime($data['startime']);
    	$data['endtime']  = strtotime($data['endtime']);
    	$data['addtime']  = time();
    	$data['editime']  = time();
    	$res              = $InfoModel->insert($data);
    	if(!$res) return $this->fail(600000);
    	return $this->success();
	}

	/**
	 * 修改
	 * [edit description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function edit(Request $request){
		//状态类型
		$request['status'] = $request['status']?1:2;
		$request['is_xcx'] = $request['is_xcx']?1:2;
		$request['is_fx']  = $request['is_fx']?1:2;

		$contacts = [];
		//处理参会咨询
		if(!empty($request['consult_title'])){
				// array('area'=>'媒体合作','name'=>'南  蕊','phone'=>'158-1079-0845','email'=>'nanrui@hmkx.cn'),
				foreach($request['consult_title'] as $k=>$v){
					$contacts[$k]['title']  = $v;
					$contacts[$k]['name']  = $request['consult_name'][$k];
					$contacts[$k]['phone'] = $request['consult_phone'][$k];
					$contacts[$k]['email'] = $request['consult_email'][$k];
				}
				$contacts = json_encode($contacts);
		}
		$request['consult_content'] = $contacts;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name'     => 'required|string',
	       'startime' => 'required|string',
	       'endtime'  => 'required|string',
	       'address'  => 'required|string',
	    ],[
            'name.required'     => '会议名称不能为空',
            'startime.required' => '开始时间不能为空',
            'endtime.required'  => '结束时间不能为空',
            'address.required'  => '地址不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$InfoModel        = new InfoModel();
    	$data             = $request->all(['type','name','content','startime','endtime','scale','money','money_content','address','address_content','consult_content','status','pc_img','wap_img','is_xcx','is_fx']);
    	$data['startime'] = strtotime($data['startime']);
    	$data['endtime']  = strtotime($data['endtime']);
    	$res              = $InfoModel->where('id',$request['id'])->update($data);
    	if(!$res) return $this->fail(600000);
    	return $this->success();
	}

	/**
	 * 删除
	 * [del description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function del(Request $request){
		 if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $res = InfoModel::whereIn('id',$ids)->update(['status'=>3]);
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

}