<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Auth\Jwt;
use App\Http\Models\AuthRule;
use Illuminate\Http\Request;
use App\Http\Models\Info;

class Api extends BaseController
{
	public static $jwt = '';//JWT验证
    public static $user = [];//用户信息
    private static $public_auth = ['auth/menu','user/userdesc','user/password','api/upload','api/content_upload','api/choiseinfo','api/choiseinfos'];//公共可以访问的权限
    private static $huiyi_auth = [
        'info_list',
        //参会人员
        'people/list','people/add','people/edit','people/del',
        //嘉宾管理
        'guest/list','guest/add','guest/edit','guest/del','guest/get_info_guest','guest/add_info','guest/del_info',

    ];//需要会议ID才访问的控制器方法
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(Request $request){

    	self::$jwt = new Jwt;
        //验证请求头是否带有JWT验证规则
        if($request->header('accesstoken')){
            self::$user = self::$jwt->verifyToken($request->header('accesstoken'));
             if(!self::$user){
                exit(json_encode(['code'=>1001,'message'=>'登陆失效,请重新登录','data'=>[]]));
            }
            //权限监控
            $xdauth = self::$user;
            if($xdauth['rules'] !== '*'){
                $path = substr($request->path(),4);
                $res = AuthRule::where(['status'=>1])
                        ->whereIn('id',explode(',',$xdauth['rules']))
                        ->orderBy('weight')
                        ->get(['url'])
                        ->toArray();
                foreach ($res as $key => $value) {
                    $arr[] = $value['url']; 
                }
                if(!in_array($path,$arr) && !in_array($path, self::$public_auth)){
                     exit(json_encode(['code'=>500000,'message'=>'没有访问权限！','data'=>[]]));
                }
                //判断会议ID才访问的控制器方法
                if(in_array($path,self::$huiyi_auth) && !isset($xdauth['hid'])){
                   exit(json_encode(['code'=>500001,'message'=>'请先选择会议！','data'=>[]]));
                }

            }
        }else{
            $path = $request->path();
            if($path != "api/user/login" && $path != "api/api/content_upload"){
                exit(json_encode(['code'=>500000,'message'=>'非法请求','data'=>[]]));
            }
        }
      
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password  密码
     * @param string $salt      密码盐
     * @return string
     */
    public function getEncryptPassword($password)
    {
        return md5(md5($password));
    }
    /**
     * 成功返回
     * [success description]
     * @author XD
     * @Date   2018-10-11
     * @param  array      $data [description]
     * @return [type]           [description]
     */
     public function success($data = [],$count = 0)
    {
        return response()->json([
            'code'    => 0,
            'count'   => $count,
            'message' => config('errorcode.code')[0],
            'data'    => $data,
        ]);
    }
    /**
     * 失败返回
     * [fail description]
     * @author XD
     * @Date   2018-10-11
     * @param  [type]     $code [description]
     * @param  array      $data [description]
     * @return [type]           [description]
     */
    public function fail($code,$msg=false,$data = [])
    {
        return response()->json([
            'code'    => $code,
            'message' => $msg?$msg:config('errorcode.code')[(int) $code],
            'data'    => $data,
        ]);
    }

    /**
     * 处理成树形结构
     * [genTree description]
     * @author XD
     * @Date   2018-10-15
     * @param  [type]     $items [description]
     * @param  string     $pid   [description]
     * @return [type]            [description]
     */
    public function genTree($items,$pid ='pid') {
        $map  = [];
        $tree = [];    
        foreach ($items as &$it){ 
            $map[$it['id']] = &$it; 
        }  //数据的ID名生成新的引用索引
        
        foreach ($items as &$it){
            $parent = &$map[$it[$pid]];
            if($parent) {
                $parent['list'][] = &$it;
            }else{
                $tree[] = &$it;
            }
        }
        return $tree;
    }

    /**
     * 处理数组
     * [genTrees description]
     * @author XD
     * @Date   2018-10-16
     * @param  [type]     $arr   [description]
     * @param  integer    $pid   [description]
     * @param  integer    $level [description]
     * @return [type]            [description]
     */
    public function genTrees($arr,$pid=0,$level=''){
         $subs = array();
         foreach ($arr as $value) {
             if($value['pid'] == $pid){
                 $value['level'] = $level;
                 $subs[] =  $value;
                 $subs = array_merge($subs,$this->genTrees($arr,$value['id'],$level.'└'));
             }
         }
         return $subs;
    }

    /**
     * 选择会议(列表)
     * [choiseinfo description]
     * @author XD
     * @desc
     * @Date   2018-12-05
     * @return [type]     [description]
     */
    public function choiseinfo(){
        $user = self::$user;
        //实例化参会人员模型
        $InfoModel  = new Info();
        $data = $InfoModel
                    ->where('status',1)
                    ->select('name','id')
                    ->get()
                    ->toArray();
        $name = '请先选择会议';
        if(isset($user['hid'])){
            foreach ($data as $key => $value) {
                if($value['id'] == $user['hid']){
                    $name = $value['name'];
                    unset($data[$key]);
                }
            }
        }
        $res['data'] = $data;
        $res['name'] = $name;
        return $this->success($res);
    }
    /**
     * 选择会议(列表)
     * [choiseinfo description]
     * @author XD
     * @desc
     * @Date   2018-12-05
     * @return [type]     [description]
     */
    public function choiseinfos(Request $request){
        $jwt  = self::$jwt;
        $user = self::$user;
        $user['hid'] = $request['info_id'];
        $token=$jwt->getToken($user);

        return $this->success(['accesstoken'=>$token]);
    }

    /**
     * 公共上传类
     * [upload description]
     * @author XD
     * @desc
     * @Date   2018-11-30
     * @return [type]     [description]
     */
    public function upload(Request $request){  
        // vendor('aliyun-oss-php-sdk.autoload');
        //oss上传 
        $bucketName = config('oss.OSS_TEST_BUCKET'); 
        $ossClient = new \OSS\OssClient(config('oss.OSS_ACCESS_ID'), config('oss.OSS_ACCESS_KEY'), config('oss.OSS_ENDPOINT'), false); 
        $web=config('oss.OSS_WEB_SITE'); 
        //图片
        $file = $_FILES[$request->header('filename')];  
        // $file = $request->file('file');
        if (empty($file)) {
           return $this->fail(500,'图片不存在!');
        }
        $rs=$this->ossUpPic($file,'xbhwt',$ossClient,$bucketName,$web,1);  
        if($rs['code']==1){ 
            //图片  
            return $this->success([
                'url' => $rs['msg'],
                'thumb'=>$rs['thumb']
            ]);           
        }else{ 
            return $this->fail(500,'图片有误：'.$rs['msg']); 
        }  
    }

    /**
     * 编辑器里的图片上传
     * [content_upload description]
     * @author XD
     * @desc
     * @Date   2018-12-05
     * @param  Request    $request [description]
     * @return [type]              [description]
     */
    public function content_upload(Request $request){
          //oss上传 
        $bucketName = config('oss.OSS_TEST_BUCKET'); 
        $ossClient = new \OSS\OssClient(config('oss.OSS_ACCESS_ID'), config('oss.OSS_ACCESS_KEY'), config('oss.OSS_ENDPOINT'), false); 
        $web=config('oss.OSS_WEB_SITE'); 
        //图片
        $file = $_FILES['file'];  
        // $file = $request->file('file');
        if (empty($file)) {
           return $this->fail(500,'图片不存在!');
        }
        $rs=$this->ossUpPic($file,'xbhwt',$ossClient,$bucketName,$web,1);  
        if($rs['code']==1){ 
            //图片  
            return $this->success([
                'src' => $rs['thumb'],
                'thumb'=>$rs['thumb']
            ]);           
        }else{ 
            return $this->fail(500,'图片有误：'.$rs['msg']); 
        }  
    }
        //oss上传 
        /* 
         *$fFiles:文件域 
         *$n：上传的路径目录 
         *$ossClient   
         *$bucketName 
         *$web:oss访问地址 
         *$isThumb:是否缩略图 
         */ 
        public function ossUpPic($fFiles,$n,$ossClient,$bucketName,$web,$isThumb=0){ 
            $fType=$fFiles['type']; 
            $back=array( 
                'code'=>0, 
                'msg'=>'', 
            ); 
            // if(!in_array($fType, config('oss_exts'))){ 
            //     $back['msg']='文件格式不正确'; 
            //     return $back; 
            //     exit; 
            // } 
            $fSize=$fFiles['size']; 
            if($fSize>config('oss.oss_maxSize')){ 
                $back['msg']='文件超过了限制'; 
                return $back; 
                exit; 
            } 
             
            $fname=$fFiles['name']; 
            $ext=substr($fname,stripos($fname,'.')); 
             
            $fup_n=$fFiles['tmp_name']; 
            $file_n=time().'_'.rand(100,999); 
            $object = $n."/".$file_n.$ext;//目标文件名 
             
         
            if (is_null($ossClient)) exit(1);     
            $ossClient->uploadFile($bucketName, $object, $fup_n); 
            if($isThumb==1){ 
                // 图片缩放，参考https://help.aliyun.com/document_detail/44688.html?spm=5176.doc32174.6.481.RScf0S  
                $back['thumb']= $web.$object."?x-oss-process=image/resize,h_300,w_300"; 
            }     
            $back['code']=1; 
            $back['msg']=$web.$object; 
            return $back; 
            exit;     
        }
}
