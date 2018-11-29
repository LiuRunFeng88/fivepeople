<?php
/**
 * Created by 刘先生
 * 2018/11/29 16:05
 */

namespace app\common\model;


use think\Cache;
use think\Model;

class CategorySecond extends Model {

    protected $table = 'category_second';

    public static $err_code;

    public static $category_second;
    public static function getCategorySecondList(){
        $category_second_list = Cache::get('category_second_list');
        if (empty($category_second_list)){
            $category_second_list = self::where(['status'=>1])->field('id,father_id,name,img_url')->select();
            Cache::set('category_second_list',$category_second_list,300);
        }
        return $category_second_list;
    }

    public static function getCategorySecondByFatherId($id){
        if(empty(self::$category_second)){
            $list = self::getCategorySecondList();
            foreach ($list as $row){
                $params[$row['father_id']][] = $row;
            }
        }
        return $params[$id];
    }

}