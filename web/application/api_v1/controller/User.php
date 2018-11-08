<?php
/**
 * Created by 刘先生
 * 2018/11/8 10:34
 */

namespace app\api_v1\controller;


use app\common\controller\BaseController;

class User extends BaseController {

    public function user_info(){
        try{
            $this->_checkAppRequest();
            $users = $this->_user;
            unset($users['password']);
            return suc_return(['user_info'=>$users]);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }

}