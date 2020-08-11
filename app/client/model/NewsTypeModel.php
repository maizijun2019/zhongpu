<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/1
 * Time: 15:08
 */

namespace app\client\model;

use think\admin\extend\DataExtend;
use think\facade\Db;

class NewsTypeModel
{
    public static $TABLE_NAME = "zp_news_type";

    public static function subTypeId(int $news_type_id){
        $sub_type_ids[$news_type_id] = 1;
        $tree = DataExtend::arr2table(Db::table(self::$TABLE_NAME)
            -> where("news_type_id",">",$news_type_id)
            -> field(array("news_type_id","pid"))
            -> order("news_type_id","asc")
            -> select() -> toArray(),"news_type_id");
        foreach ($tree as $vo){
            if(!empty($sub_type_ids[$vo["pid"]])){
                $sub_type_ids[$vo["news_type_id"]] = 1;
            }
        }
        return array_keys($sub_type_ids);
    }
}