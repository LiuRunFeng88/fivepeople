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
    //商品列表
    public function product_list(){
        try{
            $this->_checkAppRequest();
            $whereArr = array();
            //筛选
            if (!empty($this->_data['keyword'])){
                $keyword = $this->_data['keyword'];
                $whereArr['name'] = ['LIKE',"%$keyword%"];
            }
            if (!empty($this->_data['classify_id'])){
                $whereArr['classify_id'] = $this->_data['classify_id'];
            }
            $this->_data['type'] = $this->_data['type'] ?? 1;
            $this->_data['page'] = $this->_data['page'] ?? 1;
            //排序
            if ($this->_data['type'] == 1){ //综合
                $orderArr = [];
            }elseif ($this->_data['type'] == 2){ //销量
                $orderArr = ['sale_quantity'=>'DESC'];
            }elseif ($this->_data['type'] == 3){ //价格
                $orderArr = ['selling_price'=>'DESC'];
            }else{ //新品
                $orderArr = ['release_at'=>'DESC'];
            }
            //获取商品列表
            $product_list = \app\common\model\Product::where($whereArr)->order($orderArr)->paginate(null,false,['page'=>$this->_data['page']]);
            $data = $product_list->toArray();
            $data = $data['data'];
            foreach ($data as $index=>$product){
                $data[$index]['pics'] = explode(',',$product['pics']);
                $data[$index]['medias'] = explode(',',$product['medias']);
            }
            $result = [
                'total' => $product_list->total(),
                'per_page' => config('paginate.list_rows'),
                'current_page' => $product_list->currentPage(),
                'list'=>$data
            ];
            return suc_return($result);
        }catch (\Exception $e){
            return err_return($e->getCode(),$e->getMessage());
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