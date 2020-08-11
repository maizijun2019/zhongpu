<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/1
 * Time: 10:03
 */

namespace app\client\model;

use think\admin\extend\DataExtend;
use think\facade\Db;

class ProjectLevelModel
{
    public static $TABLE_NAME = "zp_project_level";

    public static function subLevelId(int $project_level_id){
        $level_ids[$project_level_id] = 1;
        $tree = DataExtend::arr2table(Db::table(self::$TABLE_NAME)
            -> where("project_level_id",">",$project_level_id)
            -> field(array("project_level_id","pid"))
            -> order("project_level_id","asc")
            -> select() -> toArray(),"project_level_id");
        foreach ($tree as $vo){
            if(!empty($level_ids[$vo["pid"]])){
                $level_ids[$vo["project_level_id"]] = 1;
            }
        }
        return array_keys($level_ids);
    }
}