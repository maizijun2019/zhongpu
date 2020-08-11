<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/1
 * Time: 10:01
 */

namespace app\client\controller;

use app\client\model\ConsumerModel;
use app\client\model\EnterpriseModel;
use app\client\model\ProjectAttributeModel;
use app\client\model\ProjectLevelModel;
use app\client\model\ProjectModel;
use app\client\model\RegionModel;
use app\client\utils\JSONUtil;
use app\client\utils\LimitPageUtil;
use think\admin\Controller;
use think\facade\Db;

class Project extends Controller
{
    /**
     * 项目分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function typeList(){
        $list = Db::table(ProjectLevelModel::$TABLE_NAME)
            -> where("pid",0)
            -> field(array("project_level_id","title"))
            -> order("project_level_id","desc")
            -> select() -> toArray();
        JSONUtil::sendSuccess($list);
    }

    /**
     * 项目列表
     * @param string $keyword 关键词
     * @param int $project_level_id 项目分类id
     * @param int $page 页数
     * @param int $sum 条数
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function projectList(string $keyword = "",int $project_level_id = 0,int $page = DEFAULT_PAGE,int $sum = DEFAULT_SUM){
        $where = array();
        $wherein = array();
        if(strlen($keyword) > 0){
            array_push($where,array("a.project_name","LIKE","%{$keyword}%"));
        }
        if($project_level_id > 0){
            $wherein = array("a.project_level_id",ProjectLevelModel::subLevelId($project_level_id));
        }
        $data = LimitPageUtil::limitQuery($page,$sum,ProjectModel::$TABLE_NAME,"a",
            array(array("join" => "leftjoin","table_name" => ProjectLevelModel::$TABLE_NAME." b","on" => "a.project_level_id = b.project_level_id")),
            $where,$wherein,
            array("a.project_id","a.project_name","a.province_region_id","a.city_region_id","a.area_region_id","IFNULL(b.title,'') title","a.create_time"),
            "a.project_id");
        $region_ids = array();
        foreach ($data["list"] as $key => &$vo){
            if(isset($vo["create_time"])){
                $vo["create_time_text"] = date("Y-m-d",$vo["create_time"]);
                unset($vo["create_time"]);
            }
            if(!empty($vo["province_region_id"])){
                if(empty($region_ids[$vo["province_region_id"]])){
                    $region_ids[$vo["province_region_id"]] = array($key);
                }else{
                    array_push($region_ids[$vo["province_region_id"]],$key);
                }
                $vo["province_region_name"] = "";
                unset($vo["province_region_id"]);
            }
            if(!empty($vo["city_region_id"])){
                if(empty($region_ids[$vo["city_region_id"]])){
                    $region_ids[$vo["city_region_id"]] = array($key);
                }else{
                    array_push($region_ids[$vo["city_region_id"]],$key);
                }
                $vo["city_region_name"] = "";
                unset($vo["city_region_id"]);
            }
            if(!empty($vo["area_region_id"])){
                if(empty($region_ids[$vo["area_region_id"]])){
                    $region_ids[$vo["area_region_id"]] = array($key);
                }else{
                    array_push($region_ids[$vo["area_region_id"]],$key);
                }
                $vo["area_region_name"] = "";
                unset($vo["area_region_id"]);
            }
        }
        if(sizeof($region_ids) > 0){
            $regions = Db::table(RegionModel::$TABLE_NAME)
                -> whereIn("region_id",array_keys($region_ids))
                -> field(array("region_id","level","name"))
                -> limit(sizeof($region_ids)) -> select() -> toArray();
            foreach ($regions as $region){
                $indexs = $region_ids[$region["region_id"]];
                foreach ($indexs as $index){
                    switch ($region["level"]){
                        case RegionModel::$PROVINCE_LEVEL:
                            $data["list"][$index]["province_region_name"] = $region["name"];
                            break;
                        case RegionModel::$CITY_LEVEL:
                            $data["list"][$index]["city_region_name"] = $region["name"];
                            break;
                        case RegionModel::$AREA_LEVEL:
                            $data["list"][$index]["area_region_name"] = $region["name"];
                            break;
                    }
                }
            }
        }
        JSONUtil::sendSuccess($data);
    }

    /**
     * 项目详情
     * @param int $project_id 项目id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info(int $project_id){
        $data = array();
        if($project_id > 0){
            $data = Db::table(ProjectModel::$TABLE_NAME) -> alias("a")
                -> leftJoin(ProjectLevelModel::$TABLE_NAME." b","a.project_level_id = b.project_level_id")
                -> where("a.project_id",$project_id)
                -> field(array("a.*","b.title")) -> find();
            if($data === null){
                $data = array();
            }else{
                $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
                unset($data["create_time"]);
                unset($data["project_level_id"]);
                $regions = Db::table(RegionModel::$TABLE_NAME)
                    -> whereIn("region_id",array($data["province_region_id"],$data["city_region_id"],$data["area_region_id"]))
                    -> field(array("level","name"))
                    -> limit(3) -> select() -> toArray();
                foreach ($regions as $region){
                    switch ($region["level"]){
                        case RegionModel::$PROVINCE_LEVEL:
                            $data["province_region_name"] = $region["name"];
                            unset($data["province_region_id"]);
                            break;
                        case RegionModel::$CITY_LEVEL:
                            $data["city_region_name"] = $region["name"];
                            unset($data["city_region_id"]);
                            break;
                        case RegionModel::$AREA_LEVEL:
                            $data["area_region_name"] = $region["name"];
                            unset($data["area_region_id"]);
                            break;
                    }
                }
            }
        }
        JSONUtil::sendSuccess($data);
    }

    /**
     * 获取属性
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function attribute(){
        $data = Db::table(ProjectAttributeModel::$TABLE_NAME)
            -> where(array("display" => "Y"))
            -> field(array("project_attribute_id","attribute_name","company","required","remarks"))
            -> select() -> toArray();
        JSONUtil::sendSuccess($data);
    }

    public function matching(int $enterprise_id){
        $consumer = ConsumerModel::checkLogin(true);
        $enterprise_attribute = Db::table(EnterpriseModel::$TABLE_NAME)
            -> where(array("enterprise_id" => $enterprise_id,"consumer_id" => $consumer["consumer_id"]))
            -> field(array("attribute"))
            -> find();
        $level_id = Db::table(ProjectLevelModel::$TABLE_NAME);
        if(sizeof($enterprise_attribute["attribute"]) > 0){
            $keys = array_keys(json_decode($enterprise_attribute["attribute"],true));
            $JSON_CONTAINS_PATH = "JSON_CONTAINS_PATH(attribute,'all'";
            foreach ($keys as $key){
                $JSON_CONTAINS_PATH .= ",'$.{$key}'";
            }
            $JSON_CONTAINS_PATH .= ")";
            $level_id -> where($JSON_CONTAINS_PATH);
        }
        $level_id = $level_id -> field(array("project_level_id"))
            -> order("project_level_id","DESC") -> select() -> toArray();
        $level_array = array();
        foreach ($level_id as $id){
            array_push($level_array,$id["project_level_id"]);
        }
        if(sizeof($level_array) > 0){
            $projects = Db::table(ProjectModel::$TABLE_NAME)
                -> whereIn("project_level_id",$level_array)
                -> field(array("attribute"))
                -> limit(sizeof($level_array))
                -> select() -> toArray();
            foreach ($projects as $project){
                $attribute = json_decode($project["attribute"],true);
            }
        }
    }
}