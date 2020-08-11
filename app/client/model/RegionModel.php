<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 15:02
 */

namespace app\client\model;

use Exception;
use think\facade\Db;

class RegionModel
{
    public static $TABLE_NAME = "zp_region";
    public static $PROVINCE_LEVEL = 1;
    public static $CITY_LEVEL = 2;
    public static $AREA_LEVEL = 3;

    /**
     * 查询列表
     * @param int $level 等级
     * @param int $pid 上级id
     * @param array $field 查询字段
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function queryList(int $level,int $pid = 0,array $field = array("region_id","region_pid","name","level")){
        $sql = Db::table(self::$TABLE_NAME) -> where("level",$level);
        switch ($level){
            case self::$PROVINCE_LEVEL:
                break;
            case self::$CITY_LEVEL:
            case self::$AREA_LEVEL:
                if($pid > 0){
                    $sql = $sql -> where("region_pid",$pid);
                }
                break;
            default:
                throw new Exception("not existent level");
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        return $sql -> select() -> toArray();
    }

    /**
     * 查询是否存在
     * @param int $level 等级
     * @param int $region_id id
     * @return bool true：存在，false：不存在
     */
    public static function existence(int $level,int $region_id){
        if(Db::table(self::$TABLE_NAME) -> where(array("level" => $level,"region_id" => $region_id)) -> count() > 0){
            return true;
        }
        return false;
    }
}