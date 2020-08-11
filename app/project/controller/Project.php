<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/3
 * Time: 14:45
 */

namespace app\project\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 数据列表
 * Class Project
 * @package app\project\controller
 */
class Project extends Controller
{
    protected $table = "zp_project";
    private $project_level_table = "zp_project_level";
    private $region_table = "zp_region";

    protected function _index_page_filter(&$data){
        for($i = 0;$i < sizeof($data);$i++){
            $this -> handlerData($data[$i],true);
        }
    }

    private function handlerData(array &$data,bool $display = false){
        if($display){
            if(!empty($data["create_time"])){
                $data["create_time_text"] = date("Y-m-d H:i:s",$data["create_time"]);
                unset($data["create_time"]);
            }
            $region_ids = array();
            if(!empty($data["province_region_id"])){
                $data["province_text"] = "";
                array_push($region_ids,$data["province_region_id"]);
            }
            if(!empty($data["city_region_id"])){
                $data["city_text"] = "";
                array_push($region_ids,$data["city_region_id"]);
            }
            if(!empty($data["area_region_id"])){
                $data["area_text"] = "";
                array_push($region_ids,$data["area_region_id"]);
            }
            if(sizeof($region_ids) > 0){
                $regions = Db::table($this -> region_table)
                    -> whereIn("region_id",$region_ids)
                    -> field(array("region_id","name"))
                    -> limit(sizeof($region_ids)) -> select();
                for($i = 0;$i < sizeof($regions);$i++){
                    if($data["province_region_id"] == $regions[$i]["region_id"]){
                        $data["province_text"] = $regions[$i]["name"]."、";
                        continue;
                    }
                    if($data["city_region_id"] == $regions[$i]["region_id"]){
                        $data["city_text"] = $regions[$i]["name"]."、";
                        continue;
                    }
                    if($data["area_region_id"] == $regions[$i]["region_id"]){
                        $data["area_text"] = $regions[$i]["name"];
                        continue;
                    }
                }
            }
        }else{
        }
    }

    public function levelArray(){
        return json_encode($this -> levelTree());
    }

    private function levelTree(array $where = array(),array $field = array("project_level_id","pid","title")){
        $tree = Db::table($this -> project_level_table);
        if(sizeof($where) > 0){
            $tree = $tree -> where($where);
        }
        if(sizeof($field) > 0){
            $tree = $tree -> field($field);
        }
        return DataExtend::arr2table($tree -> order("project_level_id","asc") -> select() -> toArray(),"project_level_id");
    }

    private function subLevel(int $pid){
        $level_ids[$pid] = 1;
        $tree = $this -> levelTree(array(array("project_level_id",">",$pid)),array("project_level_id","pid"));
        foreach ($tree as $vo){
            if(!empty($level_ids[$vo["pid"]])){
                $level_ids[$vo["project_level_id"]] = 1;
            }
        }
        return array_keys($level_ids);
    }

    /**
     * 显示列表
     * @menu true
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "数据列表";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table) -> alias("a")
            -> leftJoin($this -> project_level_table." b","a.project_level_id = b.project_level_id");
        if(isset($params["project_name"]) && strlen($params["project_name"]) > 0){
            $query -> where("a.project_name",$params["project_name"]);
        }
        if(isset($params["project_level_id"]) && $params["project_level_id"] != "all"){
            $query -> whereIn("b.project_level_id",$this -> subLevel($params["project_level_id"]));
        }
        if(isset($params["province_region_id"]) && $params["province_region_id"] != "all"){
            $query -> where("province_region_id",$params["province_region_id"]);
            if(isset($params["city_region_id"]) && $params["city_region_id"] != "all"){
                $query -> where("city_region_id",$params["city_region_id"]);
                if(isset($params["area_region_id"]) && $params["area_region_id"] != "all"){
                    $query -> where("area_region_id",$params["area_region_id"]);
                }
            }
        }
        $query -> field(array("a.project_id","a.project_name","a.province_region_id",
            "a.city_region_id","a.area_region_id","a.remarks","a.create_time","b.title"))
            -> order("a.project_id","desc")
            -> page(true, true, false, 0);
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $this -> fetch("add");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(isset($params["project_level_id"])){
                $count = Db::table($this -> project_level_table) -> where("pid",$params["project_level_id"]) -> count();
                if($count > 0){
                    $this -> error("只允许最下级添加项目");
                }
            }
            if(empty($params["province_region_id"])){
                $this -> error("请选择省");
            }
            if(empty($params["city_region_id"])){
                $this -> error("请选择市");
            }
            if(empty($params["area_region_id"])){
                $this -> error("请选择区");
            }
            $params["create_time"] = time();
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $project_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($project_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("project_id",$project_id) -> find();
            $this -> fetch("edit",array(
                "vo" => $vo
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(isset($params["project_level_id"])){
                $count = Db::table($this -> project_level_table) -> where("pid",$params["project_level_id"]) -> count();
                if($count > 0){
                    $this -> error("只允许最下级添加项目");
                }
            }
            if(empty($params["province_region_id"])){
                $this -> error("请选择省");
            }
            if(empty($params["city_region_id"])){
                $this -> error("请选择市");
            }
            if(empty($params["area_region_id"])){
                $this -> error("请选择区");
            }
            if(isset($params["project_id"])){
                unset($params["project_id"]);
            }
            Db::table($this -> table) -> where("project_id",$project_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    public function attribute($project_id){

    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $project_id
     * @throws \think\db\exception\DbException
     */
    public function remove($project_id){
        $this->_applyFormToken();
        Db::table($this -> table) -> where("project_id",$project_id) -> delete() > 0 ? $this -> success("删除成功") : $this -> error("删除失败,该数据不存在");
    }
}