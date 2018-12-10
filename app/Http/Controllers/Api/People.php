<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Http\Request;
use App\Http\Models\People as PeopleModel;
use App\Http\Models\InfoTicket;
use App\Http\Models\Info;
use App\Http\Models\PeopleType;
use App\Http\Models\PeopleSign;
use Validator;
//参会人员管理
class People extends Api
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
		//实例化参会人员模型
		$people           = new PeopleModel();
		$user = self::$user;
		if(!isset($user['hid'])){
			$res = $people
				->with('sign')
				->with('info')
				->with('type')
				->with('ticket')
				->where('status',1)
				->where(function ($query) use ($request) {
				    $request['name'] && $query->where('name', 'like' , '%'.$request['name'].'%');
				    $request['phone'] && $query->where('phone',  'like' , '%'.$request['phone'].'%');})
				->get()
				->toArray();
		}else{
			$res = $people
				->with('sign')
				->with('info')
				->with('type')
				->with('ticket')
				->where('status',1)
				->where('hid',$user['hid'])
				->where(function ($query) use ($request) {
				    $request['name'] && $query->where('name', 'like' , '%'.$request['name'].'%');
				    $request['phone'] && $query->where('phone',  'like' , '%'.$request['phone'].'%');})
				->get()
				->toArray();
		}
	
		return $this->success($res);
	}

	/**
	 * 获取门票类别
	 * [get_ticket description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function get_ticket(){
		$InfoTicket = new InfoTicket();

		$res = $InfoTicket->where('status',1)->select('id','name')->get()->toArray();
		return $this->success($res);
	}

	/**
	 * 获取参会类别
	 * [type description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function get_type(){
		$PeopleType = new PeopleType();
		$res = $PeopleType->get()->toArray();
		return $this->success($res);
	}
	/**
	 * 获取参会类别
	 * [type description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 * @return [type]     [description]
	 */
	public function get_info(){
		$Info = new Info();
		$res = $Info->where('status',1)->select('id','name')->get()->toArray();
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
		$type['is_pay']  = $request['is_pay']?2:1;
		$type['is_sign'] = $request['is_sign']?2:1;
		$type['is_file'] = $request['is_file']?2:1;
		$type['is_fp']   = $request['is_fp']?2:1;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name'  => 'required|string',
	       'phone' => 'required|string',
	       'money' => 'required|string',
	       'num'   => 'required|integer',
	       'card'  => 'required|string',
	    ],[
            'name.required'  => '姓名不能为空',
            'phone.required' => '手机号不能为空',
            'money.required' => '总费用不能为空不能为空',
            'num.required'   => '人数不能为空',
            'card.required'  => '身份证号码不能为空',
        ]);
        $user = self::$user;
		if(!isset($user['hid'])){
			 return $this->fail(700000);
		}
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$PeopleModel     = new PeopleModel();
    	$data            = $request->all(['tid','typeid','phone','name','money','email','num','title','unit','dept','card','vip','pay_type']);
    	$data['addtime'] = time();
    	$data['editime'] = time();
    	$data['hid']     = $user['hid'];
    	$people_id       = $PeopleModel->insertGetId($data);
    	if(!$people_id) return $this->fail(600000);
    	$type['pid'] = $people_id;
    	$PeopleSign = PeopleSign::insert($type);
    	if(!$PeopleSign){
    		$PeopleModel->where('id',$people_id)->delete();
    		 return $this->fail(600000);
    	}else{
    		return $this->success();
    	}
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
		$type['is_pay']  = $request['is_pay']?2:1;
		$type['is_sign'] = $request['is_sign']?2:1;
		$type['is_file'] = $request['is_file']?2:1;
		$type['is_fp']   = $request['is_fp']?2:1;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name'  => 'required|string',
	       'phone' => 'required|string',
	       'money' => 'required|string',
	       'num'   => 'required|integer',
	       'card'  => 'required|string',
	    ],[
            'name.required'  => '姓名不能为空',
            'phone.required' => '手机号不能为空',
            'money.required' => '总费用不能为空不能为空',
            'num.required'   => '人数不能为空',
            'card.required'  => '身份证号码不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$PeopleModel             = new PeopleModel();
    	$data              = $request->all(['tid','typeid','phone','name','money','email','num','title','unit','dept','card','vip','pay_type']);
    	$people_id          = $PeopleModel->where('id',$request['id'])->update($data);
    	if(!$people_id) return $this->fail(600000);
    	$PeopleSign = PeopleSign::where('pid',$request['id'])->update($type);
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
        $res = PeopleModel::whereIn('id',$ids)->update(['status'=>2]);
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

}