<?php
/**
 * Created by 刘先生
 * 2018/11/7 10:26
 */

namespace app\api_v1\controller;


use app\common\controller\BaseController;
use app\common\model\ErrorCode;
use think\Cache;

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
//            unset($users['id']);
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
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }
            $is_register = \app\common\model\Users::is_register($this->_data['mobile']);
            if ($this->_data['intent'] == 1) {
                if (!empty($is_register)) {
                    throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_MOBILE_REGISTERED), ErrorCode::ERROR_MOBILE_REGISTERED);
                }
            }else{
                if (empty($is_register)){
                    throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_MOBILE_NO_REGISTERED), ErrorCode::ERROR_MOBILE_NO_REGISTERED);
                }
            }
            $code = send_sms_code($this->_data['mobile'],$this->_data['intent']);
            return suc_return(['code'=>$code]);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //验证验证码
    public function phone_validate(){
        try{
            $this->_checkAppRequest();
            if(empty($this->_data['mobile']) && empty($this->_data['intent']) && empty($this->_data['code'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }

            if ($this->_data['intent'] == 1){
                $code = Cache::get(REGISTER_CODE.$this->_data['mobile']);
            }elseif ($this->_data['intent'] == 2){
                $code = Cache::get(RESET_PASSWORD.$this->_data['mobile']);
            }else{
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if ($code != $this->_data['code']){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_CODE),ErrorCode::ERROR_LOGIN_CODE);
            }
            return suc_return();
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //注册
    public function account_register(){
        try{
            $this->_checkAppRequest();

            if(empty($this->_data['mobile']) && empty($this->_data['password']) && empty($this->_data['code'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }
            $is_register = \app\common\model\Users::is_register($this->_data['mobile']);
            if (!empty($is_register)){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_MOBILE_REGISTERED),ErrorCode::ERROR_MOBILE_REGISTERED);
            }
            $code = Cache::get(REGISTER_CODE.$this->_data['mobile']);
            if ($this->_data['code'] == $code){
                Cache::rm(REGISTER_CODE.$this->_data['mobile']);
                $users = new \app\common\model\Users([
                    'nickname'=>'手机用户_'.$this->_data['mobile'],
                    'avatar_url'=>'http://118.24.222.80/fivepeopledev/web/public/img/default.jpg',
                    'mobile'=>$this->_data['mobile'],
                    'password'=>md5($this->_data['password']),
                    'register_at'=>time(),
                    'register_ip'=>get_remote_ip(),
                ]);
                $users->save();
                if (empty($users)){
                    throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INNER_ERROR),ErrorCode::INNER_ERROR);
                }
                $users = \app\common\model\Users::get($users->id);
                $token = createTokenForLoginUser($users->id);
                unset($users['password']);
//                unset($users['id']);
                return suc_return(['token'=>$token,'users'=>$users]);
            }else{
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_CODE),ErrorCode::ERROR_LOGIN_CODE);
            }
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //重置密码
    public function account_password_forgot(){
        try{
            $this->_checkAppRequest();
            if(empty($this->_data['mobile']) && empty($this->_data['password']) && empty($this->_data['code'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }
            $code = Cache::get(RESET_PASSWORD.$this->_data['mobile']);
            //校验验证码
            if ($code != $this->_data['code']) {
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_CODE),ErrorCode::ERROR_LOGIN_CODE);
            }
            //根据手机获取获取用户
            $users = \app\common\model\Users::find_user_by_mobile($this->_data['mobile']);
            if (empty($users)) {
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_MOBILE_NO_REGISTERED), ErrorCode::ERROR_MOBILE_NO_REGISTERED);
            }
            $users->password = md5($this->_data['password']);
            $users->save();
            //清除redis
            Cache::rm(RESET_PASSWORD.$this->_data['mobile']);
            return suc_return();
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //获取分类列表
    public function get_category_list(){
        try{
            $this->_checkAppRequest();
            $category_list = \app\common\model\Category::getCategoryList();
            foreach ($category_list as $key=>$value){
                $category_second = \app\common\model\CategorySecond::getCategorySecondByFatherId($value['id']);
                $category_list[$key]['second'] = $category_second;
                unset($category_second);
            }
            return suc_return(['category'=>$category_list]);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

}