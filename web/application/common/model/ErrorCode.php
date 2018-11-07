<?php
namespace app\common\model;
class ErrorCode {
    /**
    1、系统级别的错误
    100**
    2、用户级别的错误
    200**
     */
    const INNER_API_NOT_FOUND   = '10000';//接口有误
    const INNER_ERROR           = '10001';//内部错误
    const INPUT_ERROR           = '10002';//参数错误
    const INPUT_ERROR_DATA      = '10003';//DATA输入错误
    const INPUT_ERROR_COMMON    = '10004';//COMMON输入错误
    const REQUEST_EMPTR_APP_ID  = '10005';//APP_ID不能为空
    const REQUEST_ERROR_APP_ID  = '10006';//APP_ID错误
    const REQUEST_ERROR_TIME    = '10007';//无效的请求
    const REQUEST_TOKEN_NOT     = '10008';//COMMON_TOKEN不能为空
    const REQUEST_TOKEN_ERROR   = '10009';//COMMON_TOKEN无效
    const REQUEST_FREQUENT      = '10010';//请求频繁
    //用户
    const ERROR_NOT_LOGIN       = '20000';//没有登陆
    const ERROR_FAIL_LOGIN      = '20010';//登录失败,账号或密码错误
    const ERROR_LOGIN_MOBILE    = '20011';//错误的手机号
    const ERROR_LOGIN_PASSWORD  = '20012';//错误的手机登录密码
    const ERROR_LOGIN_CODE      = '20013';//错误的手机校验码
    const ERROR_LOGIN_TOKEN     = '20014';//错误的token
    const ERROR_USER_NOT_FOUNDD = '20015';//用户不存在
    const ERROR_USER_CLOSURE    = '20016';//用户被封禁，禁止登录
    const ERROR_MOBILE_REGISTERED= '20017';//手机号已注册
    //综合任务
    const TASK_RECLAIM = '32001';//请勿重复领取！
    //
    /**
     * @var []
     */
    public static $errorMap = [
        //通用
        self::INNER_API_NOT_FOUND   => '接口有误',
        self::INNER_ERROR           => '内部错误',
        self::INPUT_ERROR           => '参数错误',
        self::INPUT_ERROR_DATA      => 'DATA输入错误',
        self::INPUT_ERROR_COMMON    => 'COMMON输入错误',
        self::REQUEST_EMPTR_APP_ID  => 'APP_ID不能为空',
        self::REQUEST_ERROR_APP_ID  => 'APP_ID错误',
        self::REQUEST_ERROR_TIME    => '无效的请求',
        self::REQUEST_TOKEN_NOT     => 'COMMON_TOKEN不能为空',
        self::REQUEST_TOKEN_ERROR   => 'COMMON_TOKEN无效',
        self::REQUEST_FREQUENT      => '请求频繁',
        //用户
        self::ERROR_NOT_LOGIN       => '没有登陆',
        self::ERROR_FAIL_LOGIN      => '登录失败,账号或密码错误',
        self::ERROR_LOGIN_MOBILE    => '错误的手机号',
        self::ERROR_LOGIN_PASSWORD  => '错误的手机登录密码',
        self::ERROR_LOGIN_CODE      => '错误的手机校验码',
        self::ERROR_LOGIN_TOKEN     => '错误的token',
        self::ERROR_USER_NOT_FOUNDD => '用户不存在',
        self::ERROR_USER_CLOSURE    => '用户被封禁，禁止登录',
        self::ERROR_MOBILE_REGISTERED=> '手机号已注册',
        //综合任务
    ];
    /**
     * @param int $code
     * @return string
    */
    public static function formatErrorMsg($code)
    {
        if(!isset(self::$errorMap[$code])){
            self::$errorMap[$code] = '内部错误2';//暂时定义为未定义错误
        }
        return self::$errorMap[$code];
    }
}