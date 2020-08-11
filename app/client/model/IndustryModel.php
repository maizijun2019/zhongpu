<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 14:28
 */

namespace app\client\model;

use think\admin\extend\DataExtend;
use think\facade\Db;

class IndustryModel
{
    public static $TABLE_NAME = "zp_industry";

    /**
     * 获取行业列表
     * @param int $pid 上级行业id
     * @param array $field 查询字段
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function queryList(int $pid = 0,array $field = array()){
        $sql = Db::table(self::$TABLE_NAME);
        if($pid > 0){
            $sql = $sql -> where("pid",$pid);
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        $list = DataExtend::arr2table($sql -> order("industry_id","asc") -> select() -> toArray(),"industry_id");
        foreach ($list as &$vo){
            $vo["industry_name"] = $vo["spl"].$vo["industry_name"];
            unset($vo["spl"]);
            unset($vo["path"]);
            unset($vo["spt"]);
        }
        return $list;
    }

    /**
     * 检测是否存在
     * @param int $industry_id 行业id
     * @return bool true：存在，false：不存在
     */
    public static function existence(int $industry_id){
        if(Db::table(self::$TABLE_NAME) -> where("industry_id",$industry_id) -> count() > 0){
            return true;
        }
        return false;
    }

    /**
     * 查询是否还有下级
     * @param int $industry_id 行业id
     * @return bool true：有，false：没有
     */
    public static function lastLevel(int $industry_id){
        if(Db::table(self::$TABLE_NAME) -> where("pid",$industry_id) -> count() > 0){
            return true;
        }
        return false;
    }
}