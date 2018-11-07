<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 9:43
 */
/**
 * Created by 刘先生
 * 2018/11/7 9:43
 */

namespace app\common\controller;


use app\common\model\ErrorCode;
use think\Controller;

class BaseController extends Controller {

    protected $_user;

    protected $_common;

    protected $_data;
    /**
     * @desc 保存异常,主要用户保存 action之前产生的异常 如_initialize方法
     * @var \Exception
     */
    public $e;

    protected function _initialize() {
        try{
            // config start--------------------   json response format ---------------------
            config(['default_return_type' => 'json' , 'default_ajax_return' => 'json']);
            // config end------------------------------------------------------------------
            $this->_before_initialize();
            $this->_initRequest();
            //没有登陆了的情况
            $token =  isset($this->_data['token']) ? $this->_data['token']:'';
            if(empty($token)){
                throw new  \Exception(ErrorCode::formatErrorMsg(ErrorCode::REQUEST_TOKEN_NOT), ErrorCode::REQUEST_TOKEN_NOT);
            }
            $user_id = getUserIdForLoginUser($token);
            if(!$user_id){
                throw new  \Exception(ErrorCode::formatErrorMsg(ErrorCode::REQUEST_TOKEN_ERROR), ErrorCode::REQUEST_TOKEN_ERROR);
            }
            //用户信息
            $this->_user = \app\common\model\Users::get($user_id);
            if (empty($this->_user)) {//去用户中心拉取数据
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_USER_NOT_FOUNDD), ErrorCode::ERROR_USER_NOT_FOUNDD);
            }
            return true;
        }catch (\Exception $e){
            $this->e = $e;
        }
    }

    //初始化请求数据
    protected function _initRequest(){
        if($this->e){
            return;
        }
        try{
            $body = file_get_contents("php://input");
            if(!$body){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            //$request = \shiwan\AES_V2::DecodeRequest($body);
            $request = json_decode($body,true);
//            if (!isset($request['data'])){
//                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR_DATA),ErrorCode::INPUT_ERROR_DATA);
//            }elseif (empty($request['common'])){
//                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR_COMMON),ErrorCode::INPUT_ERROR_COMMON);
//            }
//            $this->_common =  isset($request['common']) ? $request['common'] : [];
            $this->_data =  $request;
            //unset
            unset($request);unset($body);
        }catch (\Exception $e){
            throw $e;
        }
        return;
    }

    protected function _before_initialize(){

    }

    protected function _after_initialize(){

    }

    /**
     * @desc 每个action检查是否有异常产生
     * @throws \Exception
     */
    protected function _checkAppRequest(){
        if($this->e){
            throw $this->e;
        }
    }

}