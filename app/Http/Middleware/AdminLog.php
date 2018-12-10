<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\Jwt;
class AdminLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $accesstoken = $request->header('accesstoken');
        $path = $request->path();  //操作的路由
        $input = $request->all(); //操作的内容
        $input['request'] = $request->method();  //操作的方法
        $ip = $request->ip();  //操作的IP
        if(!empty($accesstoken)){
            if(stripos($path,'edit') || stripos($path,'add') || stripos($path,'del')){
                $user = Jwt::verifyToken($accesstoken);//解密token
                $useragent = $_SERVER['HTTP_USER_AGENT'];//获取用户浏览器
                self::writeLog($user,$input,$path,$useragent,$ip);
            }else {
                return $next($request);
            }
           
        }
        return $next($request);
    }
    public  function writeLog($user,$input,$path,$useragent,$ip){
        // $user = User::where('usernum',$usernum)->first();
        if($user) {
            $user_id = $user['subid'];
            $user_name = $user['sub'];
        }else{
            $user_id = 0;
            $user_name = '';
        }
        $path     = substr($path,4);
        $auth     = new \App\Http\Models\AuthRule();
        $authres  = $auth->where('url',$path)->select('name','pid')->first()->toArray();
        $authres1 = $auth->where('id',$authres['pid'])->select('name')->first()->toArray();
        $log_name = $authres1['name'].' '.$authres['name'];
        $log      = new \App\Http\Models\AdminLogs();
        $log->setAttribute('admin_id', $user_id);
        $log->setAttribute('log_name',$log_name);
        $log->setAttribute('admin_name', $user_name);
        $log->setAttribute('url', $path);
        $log->setAttribute('useragent', $useragent);
        $log->setAttribute('ip', $ip);
        $log->setAttribute('log_content', json_encode($input, JSON_UNESCAPED_UNICODE));
        $log->save();
    }
}
