<?php
/**
 * Created by 刘先生
 * 2018/11/8 10:34
 */

namespace app\api_v1\controller;


use app\common\controller\BaseController;
use app\common\model\ErrorCode;
use think\Db;

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

    //添加收货地址
    public function add_address(){
        Db::startTrans();
        try{
            if (empty($this->_data['name']) || empty($this->_data['area']) || empty($this->_data['address'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }
            $is_default = $this->_data['is_default']??0;
            $address = array();
            $address['user_id'] = $this->_user['id'];
            $address['mobile'] = $this->_data['mobile'];
            $address['area']=$this->_data['area'];
            $address['address']=$this->_data['address'];
            $address['name'] =$this->_data['name'];
            $address['ip'] = get_remote_ip();
            $address['created_at']=time();
            //是否设为默认地址
            if ($is_default){
                //修改之前的默认地址
                \app\common\model\UsersAddress::where(['user_id'=>$this->_user['id'],'status'=>1])->update(['status'=>0]);
                 $address['status']=1;
            }else{
                $address['status'] =0;
            }
            \app\common\model\UsersAddress::create($address);
            Db::commit();
            return suc_return();
        }catch (\Exception $e){
            Db::rollback();
            return err_return($e->getCode(),$e->getMessage());
        }
    }

    //修改收货地址
    public function edit_address(){
        try{
            if (empty($this->_data['id']) || empty($this->_data['name']) || empty($this->_data['area']) || empty($this->_data['address'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            if (!preg_match('/^1[0-9]{10}$/', $this->_data['mobile'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::ERROR_LOGIN_MOBILE),ErrorCode::ERROR_LOGIN_MOBILE);
            }
            $address = \app\common\model\UsersAddress::get($this->_data['id']);
            if (empty($address)){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            $address->name = $this->_data['name'];
            $address->area = $this->_data['area'];
            $address->address = $this->_data['address'];
            $address->mobile = $this->_data['mobile'];
            $address->update_at = time();
            $address->save();
            return suc_return();
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }



}