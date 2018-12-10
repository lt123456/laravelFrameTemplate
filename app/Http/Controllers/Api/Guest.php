<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Http\Request;
use App\Http\Models\Guest as GuestModel;
use App\Http\Models\Info;
use App\Http\Models\InfoGuest;

use Validator;
//会议管理
class Guest extends Api
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
		$GuestModel  = new GuestModel();
		$res = $GuestModel
					->where(function ($query) use ($request) {
					    $request['name'] && $query->where('name', 'like' , '%'.$request['name'].'%');
					    })
					->where('status',1)
					->get()
					->toArray();
		$user = self::$user;
		if(isset($user['hid'])){
			$guest = InfoGuest::where('hid',$user['hid'])->get()->toArray();
			if($guest){
				foreach ($res as $key => $value) {
					foreach ($guest as $k => $vv) {
						 if($value['id'] == $vv['gid']){
						 	unset($res[$key]);
						 }
					}
				}
			}
		}
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
	public function get_info_guest(Request $request){
		$InfoGuest = new InfoGuest();
		$user = self::$user;
		$res = $InfoGuest->where('hid',$user['hid']??0)->with('info')->with(['guest'=>function($query) use ($request) {
					    $request['name'] && $query->where('name', 'like' , '%'.$request['name'].'%');
					    }])->get()->toArray();
		if($res){
			foreach ($res as $key => $value) {
				 if(empty($value['guest'])){
				 	unset($res[$key]);
				 }
			}
		}
		return $this->success($res);
	}
	/**
	 * 嘉宾加入当前会议
	 * [add_info description]
	 * @author XD
	 * @desc
	 * @Date   2018-12-06
	 */
	public function add_info(Request $request){
		if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $data = [];
        $user = self::$user;
        foreach ($ids as $key => $value) {
        	$data[$key]['hid'] = $user['hid']??0;
        	$data[$key]['gid'] = $value;
        }
        $res = InfoGuest::insert($data);
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}
	/**
	 * 嘉宾移除当前会议
	 * [add_info description]
	 * @author XD
	 * @desc
	 * @Date   2018-12-06
	 */
	public function del_info(Request $request){
		if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $res = InfoGuest::whereIn('id',$ids)->delete();
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}
	/**
	 * 添加
	 * [add description]
	 * @author XD
	 * @desc
	 * @Date   2018-11-26
	 */
	public function add(Request $request){
    	//验证
    	$validate = Validator::make($request->all(), [
	       'name'     => 'required|string',
	    ],[
            'name.required'     => '嘉宾名称不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$GuestModel        = new GuestModel();
    	$data             = $request->all(['name','ename','sex','title','unit','age','phone','email','content','econtent','headimg']);
    	$data['addtime']  = time();
    	$data['editime']  = time();
    	$res              = $GuestModel->insert($data);
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
			//验证
    	$validate = Validator::make($request->all(), [
	       'name'     => 'required|string',
	    ],[
            'name.required'     => '嘉宾名称不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$GuestModel        = new GuestModel();
    	$data             = $request->all(['name','ename','sex','title','unit','age','phone','email','content','econtent','headimg']);
    	$res              = $GuestModel->where('id',$request['id'])->update($data);
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
        $res = GuestModel::whereIn('id',$ids)->update((['status'=>2]));
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

}