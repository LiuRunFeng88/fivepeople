<?php
/**
 * Created by 刘先生
 * 2018/11/30 10:56
 */

namespace app\api_v1\controller;


use app\common\controller\BaseController;
use app\common\model\ErrorCode;

class Product extends BaseController {

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

    //商品详情
    public function product_details(){
        try{
            $this->_checkAppRequest();
            if (empty($this->_data['id'])){
                throw new \Exception(ErrorCode::formatErrorMsg(ErrorCode::INPUT_ERROR),ErrorCode::INPUT_ERROR);
            }
            $product = \app\common\model\Product::where(['id'=>$this->_data['id']])->find();
            $product = $product->toArray();
            $product['pics'] = explode(',',$product['pics']);
            $product['medias'] = explode(',',$product['medias']);
            //获取规格
            $product_specification = \app\common\model\ProductSpecification::where(['product_id'=>$product['id']])->select();
            foreach ($product_specification as $specification){
                //获取规格属性
                $product_specification_attribute = \app\common\model\ProductSpecificationAttribute::where(['specification_id'=>$specification['id']])->field('k,v')->select();
                $specification['attributes'] = $product_specification_attribute;
                unset($product_specification_attribute);
            }
            $product['sku'] = $product_specification;
            return suc_return(['product'=>$product]);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
        }
    }
}