<?php


return [

    /*
    |--------------------------------------------------------------------------
    |OSS配置
    |--------------------------------------------------------------------------
    |
    | The first number is error type, the second and third number is
    | product type, and it is a specific error code from fourth to
    | sixth.But the success is different.
    |
    */
    //oss配置 
    "OSS_ACCESS_ID" => 'LTAIv0gpyLQ3ywM9', 
    "OSS_ACCESS_KEY"=> '3cvBaAeG2mp2YT2xWh3K9UkysZTebk', 
    "OSS_ENDPOINT"  => 'http://oss.aliyuncs.com/', 
    "OSS_TEST_BUCKET" => 'jkj-web', 
    "OSS_WEB_SITE" =>'https://jkj-web.oss-cn-hangzhou.aliyuncs.com/',    //上面4个就不用介绍了，这个OSS_WEB_SITE是oss的bucket创建后的外网访问地址，如需二级域名，可以指向二级域名，具体可以参考阿里云控制台里面的oss  
     
    //oss文件上传配置 
    'oss_maxSize'=>119430400,    //1M 
    'oss_exts'   =>array(// 设置附件上传类型    
                    'image/jpg',  
                    'image/gif',  
                    'image/png',  
                    'image/jpeg', 
                    'application/octet-stream',//阿里云好像都是通过二进制上传，似乎上面4个后缀设置起到什么用？  
                ),
];
