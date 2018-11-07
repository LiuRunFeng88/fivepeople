<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\common\model\ErrorCode;

define('CACHE_USER_TOKEN_KEY_PREFIX','USER_TOKEN_');
define('CACHE_USER_TOKEN_ID_KEY_PREFIX', 'USER_TOKEN_FOR_ID_');
define('REGISTER_CODE', 'REGISTER_CODE_');
define('RESET_PASSWORD', 'RESET_PASSWORD_');
// 应用公共文件
/**
 * @desc 成功返回
 * @param [] $arr
 * @return []
 */
function suc_return($arr = []){
    return ['code'    => 0,'msg'=>'成功','data'      => $arr];
}
/**
 * @desc 失败返回
 * @param mixed $code
 * @param string $msg
 * @return []
 */
function err_return($code, $msg){
    if ($code == 0){
        $code = ErrorCode::INPUT_ERROR;
    }
    return ['code'	=> $code,'msg'	=> "{$msg}"];
}
//发送验证码
function send_sms_code($mobile,$intent){
    if ($intent == 1){  //注册验证码
        $code = rand(0000,9999);
        \think\Cache::set(REGISTER_CODE.$mobile,$code,3600);
        return $code;
    }else{ //重置密码验证码
        $code = rand(0000,9999);
        \think\Cache::set(RESET_PASSWORD.$mobile,$code,3600);
        return $code;
    }
}

function get_remote_ip(){
    if (!empty($_SERVER["HTTP_CLIENT_IP"])){
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }else{
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    return $ip;
}

/**
 * 给登陆用户申请token
 * @param int $user_id
 * @return string
 */
function createTokenForLoginUser($user_id){
    $token = \think\Cache::get(CACHE_USER_TOKEN_ID_KEY_PREFIX. $user_id);
    if (empty($token)) {
        $retry = 0;
        do{
            $retry += 1;
            if ($retry > 10)return false;
            $token = substr(md5(rand(1000, 9999) . $user_id . time()), 0, 16);
            $exist = \think\Cache::get(CACHE_USER_TOKEN_KEY_PREFIX . $token);
        }while(!empty($exist));
    }
    _updateUserToken($user_id,$token);
    return $token;
}

/**
 * 根据用户token返回用户 并更新token有效期
 * @param string $token
 * @return int
 */
function getUserIdForLoginUser($token){
    $user_id = \think\Cache::get(CACHE_USER_TOKEN_KEY_PREFIX. $token);
    if(empty($user_id)){
        return 0;
    }
    //更新有效期
    _updateUserToken($user_id,$token);
    return $user_id;
}
/**
 * 重新登记token
 * @param int $user_id
 * @param String $token
 */
function _updateUserToken($user_id,$token,$keep = true){
    if(!$keep){
        \think\Cache::clear(CACHE_USER_TOKEN_ID_KEY_PREFIX. $user_id);
        \think\Cache::clear(CACHE_USER_TOKEN_KEY_PREFIX. $token);
    }else {
        \think\Cache::set(CACHE_USER_TOKEN_ID_KEY_PREFIX. $user_id, $token, 3600 * 24 * 7);
        \think\Cache::set(CACHE_USER_TOKEN_KEY_PREFIX. $token, $user_id, 3600 * 24 * 7);
    }
    return true;
}