<?php
/**
 * Created by 刘先生
 * 2018/11/29 16:05
 */

namespace app\common\model;


use think\Cache;
use think\Model;

class Category extends Model {

    protected $table = 'category';

    public static $err_code;


    //获取父标签列表
    public static function getCategoryList(){
        $category_list = Cache::get('category_list');
        if (empty($category_list)){
            $category_list = self::where(['status'=>1])->field('id,name')->select();
            Cache::set('category_list',$category_list,300);
        }
        return $category_list;
    }


}