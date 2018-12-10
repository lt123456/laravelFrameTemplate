<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Models\AdminLog;
use App\Http\Models\AdminAuth;
use Validator;
class User extends Api
{
	/**
	 * 后台登陆接口
	 * [login description]
	 * @author XD
	 * @Date   2018-10-11
	 * @param  Request    $request [description]
	 * @return [type]              [description]
	 */
    public function login(Request $request){
    	//实例化管理员模型
    	$admin           = new Admin();
    	if(!$request['username'] || !$request['password']) return $this->fail(200001);
	    $data = $admin
	    	->where(['username'=>$request['username']])
	    	->where(['pwd'=>$this->getEncryptPassword($request['password'])])
	    	->first();
	    if(!$data) return $this->fail(200002);
	    //修改登录时间
	    	$admin
	    	->where(['username'=>$request['username']])
	    	->where(['pwd'=>$this->getEncryptPassword($request['password'])])
	    	->update(['logintime'=>time()]);
	    //使用JWT方式传递token
		$jwt = self::$jwt;
		//查出该用户的权限
		$res = AdminAuth::with('authgroup')->where(['admin_id'=>$data->id])->first()->toArray();
		$payload=array('iss'=>'admin','iat'=>time(),'exp'=>time()+(3600*24),'nbf'=>time(),'sub'=>$data->nickname,'subid'=>$data->id,'rules'=>$res['authgroup']['rules'],'jti'=>md5(uniqid('JWT').time()));
		$token=$jwt->getToken($payload);

		//存入管理员日志
        $path             = $request->path();  //操作的路由
		$input = $request->all(); //操作的内容
		$input['request'] = $request->method();  //操作的方法
        $ip = $request->ip();  //操作的IP
        $useragent = $_SERVER['HTTP_USER_AGENT'];//获取用户浏览器

		$log = new \App\Http\Models\AdminLogs();
        $log->setAttribute('admin_id', $data->id);
        $log->setAttribute('log_name','登陆');
        $log->setAttribute('admin_name', $data->nickname);
        $log->setAttribute('url', $path);
        $log->setAttribute('useragent', $useragent);
        $log->setAttribute('ip', $ip);
        $log->setAttribute('log_content', json_encode($input, JSON_UNESCAPED_UNICODE));
        $log->save();
    	return $this->success(['accesstoken'=>$token]);
    }

    /**
     * 修改密码
     * [password description]
     * @author XD
     * @Date   2018-11-05
     * @return [type]     [description]
     */
    public function password(Request $request){
        if(!$request['password'] || !$request['oldPassword']) return $this->fail(200001);
        if($request['password'] == $request['oldPassword']) return $this->fail(200003);
        //实例化管理员模型
        $admin           = new Admin();
        $user = self::$user;
        $res = $admin->where(['id'=>$user['subid']])
            ->where(['pwd'=>$this->getEncryptPassword($request['oldPassword'])])
            ->first();
        if(!$res) return $this->fail(200004);
        $res = $admin->where(['id'=>$user['subid']])->update(['pwd'=>$this->getEncryptPassword($request['password'])]);
       if($res){
            return $this->success([]);
        }else{
            return $this->fail(600000);
        }
    }
    /**
     * 用户信息
     * [userdesc description]
     * @author XD
     * @Date   2018-10-12
     * @return [type]     [description]
     */
    public function userdesc(){
    	$user = self::$user;
    	return $this->success(['username'=>$user['sub']]);
    }

    /**
     * 管理员列表
     * [userlist description]
     * @author XD
     * @Date   2018-10-15
     * @return [type]     [description]
     */
    public function userlist(Request $request){
    	//分页
    	$offset                = ($request['page']-1)*$request['limit'];
    	$admin                 = new Admin();
    	$list                  = $admin
    							->with('rule')
    							->where('status','<>',3)
    							->where(function ($query) use ($request) {
								    $request['adminname'] && $query->where('username', 'like' , '%'.$request['adminname'].'%');
								    $request['nickname'] && $query->where('nickname',  'like' , '%'.$request['nickname'].'%');
								})
    							->offset($offset)
    							->limit($request['limit'])
    							->get()
    							->toArray();
    	//数据总条数			
    	$count                   = $admin
    							->with('rule')
    							->where('status','<>',3)
    							->where(function ($query) use ($request) {
								    $request['adminname'] && $query->where('username', 'like' , '%'.$request['adminname'].'%');
								    $request['nickname'] && $query->where('nickname',  'like' , '%'.$request['nickname'].'%');
								})
    							->count();
    	foreach ($list as $key => $value) {
    		$list[$key]['rule'] = $value['rule'][0]['name'];
    	}
    	return $this->success($list,$count);
    }

    /**
     * 修改管理员信息
     * [edit description]
     * @author XD
     * @Date   2018-10-16
     * @return [type]     [description]
     */
    public function edit(Request $request){
    	$admin                                    = new Admin();
    	if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
    	$data                                     = $request->all(['nickname','remark','status']);
    	$res                                      = $admin->where('id',$request['id'])->update($data);
    	AdminAuth::where('admin_id',$request['id'])->update(['auth_id'=>$request['role']]);
    	if($res){
    		return $this->success([]);
    	}else{
    		return $this->fail(600000);
    	}
    }

    /**
     * 管理员添加
     * [add description]
     * @author XD
     * @Date   2018-10-16
     */
    public function add(Request $request){
    	if($request['status']) $request['status'] = 1;
    	else $request['status']                   = 0;
    	//验证
    	$validate = Validator::make($request->all(), [
	       'username' => 'required|string',
	       'pwd'      => 'required|string',
	       'nickname' => 'required|string',
	    ],[
            'username.required' => '用户名不能为空',
            'pwd.required'      => '密码不能为空',
            'nickname.required' => '昵称不能为空',
        ]);
		if($validate->fails())
		{
			   $msg = $validate->errors()->first();
			   return $this->fail(500,$msg);
		}
		$request['pwd'] = $this->getEncryptPassword($request['pwd']);
    	$admin             = new Admin();
    	$data              = $request->all(['nickname','remark','status','username','pwd']);
    	$data['addtime'] = time();
    	$data['editime']    = time();
    	$admin_id          = $admin->insertGetId($data);
    	if(!$admin_id) return $this->fail(600000);
    	$auth['admin_id'] = $admin_id;
    	$auth['auth_id']  = $request['role'];
    	$auth = AdminAuth::insert($auth);
    	if(!$auth){
    		$admin->where('id',$admin_id)->delete();
    		 return $this->fail(600000);
    	}else{
    		return $this->success();
    	}
    }

    /**
     * 删除管理员
     * [del description]
     * @author XD
     * @Date   2018-10-17
     * @return [type]     [description]
     */
    public function del(Request $request){
        if(!$request['ids']) return $this->fail(200001);
        $ids = explode(',', $request['ids']);
        $res = Admin::whereIn('id',$ids)->update(['status'=>3]);
        if(!$res) return $this->fail(1000);
        else return $this->success();
    }
}
