<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLogs extends Model
{
    protected $table = 'admin_log';
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'editime';
 	public function getAddtimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime']);
    }
 //    //记录管理员用户的行为日志信息

	// public function action_log($info='',$userid='',$status=true){

	// $data['url'] = substr(__ACTION__, strpos(__ACTION__, 'index.php')+strlen('index.php')+1);

	// $data['url']=strtolower($data['url']);

	// $data['operator'] =empty($userid)?getadminname($_SESSION["ADMIN_ID"]):getadminname($userid);

	// 	$data['operate_time'] = NOW_TIME;

	// 	$data['ip']=get_client_ip();

	// 	$node = M('auth_rule')->where(array('name'=>$data['url']))->find();//查找节点名称

	// 	if($status){

	// 	  $data['status']=0;

	// 	}else{

	// 	  $data['status']=1;

	// 	}

	// 	if(!empty($node) || !empty($info)){

	// 	if($info){

	// 	  $data['description']=$info;

	// 	}else{

	// 		  $data['description'] = $node['title'];

	// 	}

	// 	$data['url']=__ACTION__;

	// 	M('logs')->add($data);//记录日志

	// 	}

 // 	}
 //    /**
 //     * 管理员日志内容
 //     * [code description]
 //     * @author XD
 //     * @Date   2018-10-12
 //     * @param  integer    $code [description]
 //     * @return [type]           [description]
 //     */
 //    public function code($code=0){
 //    	$arr = [
 //    		0=>'未知信息',
 //    		1=>'登陆',
 //    	];
 //    	return $arr[$code];
 //    }
}
