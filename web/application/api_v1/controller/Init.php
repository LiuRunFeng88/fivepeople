<?php
/**
 * Created by 刘先生
 * 2018/11/7 10:26
 */

namespace app\api_v1\controller;


use app\common\controller\BaseController;
use app\common\model\ErrorCode;

class Init extends BaseController {

    public function _initialize(){
        try {
            // config start----------------------------------------------------------------
            //json response format
            config(['default_return_type' => 'json' , 'default_ajax_return' => 'json']);
            // config end------------------------------------------------------------------
            //request
            $this->_initRequest();
        } catch (\Exception $e) {
            //throw $e;
            $this->e = $e;
        }
    }
    //登录
    public function account_login(){
        try{
            $this->_checkAppRequest();

            if(empty($this->_data['mobile']) && empty($this->_data['password'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            $users = \app\common\model\Users::account_login($this->_data['mobile'],$this->_data['password']);
            //初始化token
            $token = createTokenForLoginUser($users->id);
            unset($users['password']);
            unset($users['id']);
            return suc_return(['token'=>$token,'users'=>$users]);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //发送验证码
    public function phone_send_code(){
        try{
            $this->_checkAppRequest();

            if(empty($this->_data['mobile']) && empty($this->_data['intent'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if ($this->_data['intent'] == 1){ //注册验证码

            }elseif ($this->_data['intent'] == 2){ //重置密码验证码

            }
            return suc_return();
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

}