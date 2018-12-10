<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Http\Request;
use App\Http\Models\InfoAgenda;
use Validator;
//日程管理
class Agenda extends Api
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
		$InfoAgenda  = new InfoAgenda();
		$user = self::$user;
		if(!isset($user['hid'])){
			$res = $InfoAgenda
					->with('info')
					->where(function ($query) use ($request) {
					    $request['name'] && $query->where('title', 'like' , '%'.$request['name'].'%');
					    })
					->orderBy('sort')
					->get()
					->toArray();
		}else{
			$res = $InfoAgenda
					->with('info')
					->where('hid',$user['hid'])
					->where(function ($query) use ($request) {
					    $request['name'] && $query->where('title', 'like' , '%'.$request['name'].'%');
					    })
					->orderBy('sort')
					->get()
					->toArray();
		}
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
		$request['display'] = $request['display']?1:2;
	
    	//验证
    	$validate = Validator::make($request->all(), [
	       'title'    => 'required|string',
	       'date'     => 'required|string',
	       'startime' => 'required|string',
	       'endtime'  => 'required|string',
	       'sort'     => 'required|integer',
	    ],[
            'title.required'     => '标题不能为空',
            'date.required'     => '活动时间不能为空',
            'startime.required' => '开始时间不能为空',
            'endtime.required'  => '结束时间不能为空',
            'sort.required'     => '地址不能为空',
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
    	$InfoAgenda       = new InfoAgenda();
    	$data             = $request->all(['title','remark','date','startime','endtime','display','sort']);
    	$data['hid']      = $user['hid'];
    	$data['startime'] = strtotime($data['date'].' '.$data['startime']);
    	$data['endtime']  = strtotime($data['date'].' '.$data['endtime']);
    	$data['date']     = strtotime($data['date']);
    	$data['addtime']  = time();
    	$data['editime']  = time();
    	$res              = $InfoAgenda->insert($data);
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
		$request['display'] = $request['display']?1:2;

		//验证
    	$validate = Validator::make($request->all(), [
	       'title'    => 'required|string',
	       'date'     => 'required|string',
	       'startime' => 'required|string',
	       'endtime'  => 'required|string',
	       'sort'     => 'required|integer',
	    ],[
            'title.required'     => '标题不能为空',
            'date.required'     => '活动时间不能为空',
            'startime.required' => '开始时间不能为空',
            'endtime.required'  => '结束时间不能为空',
            'sort.required'     => '地址不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
    	$InfoAgenda        = new InfoAgenda();
    		$data             = $request->all(['title','remark','date','startime','endtime','display','sort']);
    	$data['startime'] = strtotime($data['date'].' '.$data['startime']);
    	$data['endtime']  = strtotime($data['date'].' '.$data['endtime']);
    	$data['date']     = strtotime($data['date']);
    	$res              = $InfoAgenda->where('id',$request['id'])->update($data);
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
        $res = InfoAgenda::whereIn('id',$ids)->delete();
        if(!$res) return $this->fail(1000);
        else return $this->success();
	}

}