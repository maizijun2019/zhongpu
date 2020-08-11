<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/5
 * Time: 09:48
 */

namespace app\project\controller;

use app\client\utils\TotalUtil;
use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\App;
use think\facade\Db;

/**
 * 关系等级
 * Class Level
 * @package app\project\controller
 */
class Level extends Controller
{
    protected $table = "zp_project_level";
    private $attributeTable = "zp_project_attribute";
    private $levelTable = "zp_project_level";
    private $levelTypeTable = "zp_project_level_type";

    public function index(){
        $data = $this -> baseQuery(Db::table($this -> table),$this -> request -> get())
            -> field(array("a.*","b.name type_text"))
            -> select() -> toArray();
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
        $data = TotalUtil::list_to_tree($data,"project_level_id");
        $this -> fetch("",array("list" => $data));
    }

    protected function _index_page_filter(&$data){
        $data = DataExtend::arr2table($data,"project_level_id");
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    private function handlerData(array &$data,$display = true){
        if($display){
            if(!empty($data["attribute"])){
                $data["attributes"] = "";
                $data["attribute"] = json_decode($data["attribute"],true);
                if(sizeof($data["attribute"]) > 0){
                    $data["attributes"] = "内含属性值";
                }
            }
        }else{
            if(isset($data["project_level_id"])){
                unset($data["project_level_id"]);
            }
            if(isset($data["attribute"]) && is_array($data["attribute"])){
                $data["attribute"] = json_encode($data["attribute"]);
            }
        }
    }

    public function levelArray(){
        return json_encode($this -> levelTree());
    }

    private function levelTree(array $where = array(),array $field = array("project_level_id","pid","title")){
        $tree = Db::table($this -> levelTable);
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

    private function baseQuery($query,array $params = array()){
        $query = $query -> alias("a")
            -> leftJoin($this -> levelTypeTable." b","a.project_level_type_id = b.project_level_type_id");
        if(isset($params["project_level_id"]) && $params["project_level_id"] != "all"){
            $query = $query -> whereIn("project_level_id",$this -> subLevel($params["project_level_id"]));
        }
        return $query;
    }

    /**
     * 显示列表
     * @menu
     * @auth
     */
    public function index1(){
        $this -> title = "关系等级";
        $this -> baseQuery($this -> _query($this -> table),$this -> request -> get())
            -> fielf(array("a.*","b.name type_text"))
            -> order("project_level_id","asc") -> page(false, true,false);
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $pid = $this -> request -> get("pid");
            if(empty($pid) || $pid < 0){
                $pid = 0;
            }
            $this -> fetch("form",array(
                "tree" => $this -> levelTree(),
                "pid" => $pid
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(empty($params["attribute"])){
                $params["attribute"] = "{}";
            }
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $project_level_id
     * @throws \think\db\exception\DbException
     */
    public function edit($project_level_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $tree = $this -> levelTree();
            $vo = array();
            foreach ($tree as $item){
                if($item["project_level_id"] == $project_level_id){
                    $vo = $item;
                    break;
                }
            }
            $this -> fetch("form",array(
                "tree" => $tree,
                "vo" => $vo,
                "pid" => $vo["pid"]
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table) -> where("project_level_id",$project_level_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 属性
     * @menu true
     * @auth true
     * @param $project_level_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function attribute($project_level_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $count = Db::table($this -> table)
                -> where("pid",$project_level_id)
                -> count();
            if($count > 0){
                $this -> error("仅支持最下级编辑属性");
            }
            $vo = Db::table($this -> table)
                -> where("project_level_id",$project_level_id)
                -> field(array("attribute")) -> find();
            $this -> handlerData($vo);
            $this -> fetch("",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params["attribute"] = $this -> request -> post("attribute");
            $this -> handlerData($params,false);
            Db::table($this -> table) -> where("project_level_id",$project_level_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $project_level_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function remove($project_level_id){
        $map = array($project_level_id => 1);
        $tree = DataExtend::arr2table(Db::table($this -> table) -> where("project_level_id",">",$project_level_id) -> field(array("project_level_id","pid")) -> order("project_level_id","asc") -> select() -> toArray(),"project_level_id");
        foreach ($tree as $vo){
            if(!empty($map[$vo["pid"]])){
                array_push($map,array($vo["project_level_id"] => 1));
            }
        }
        $ids = array_keys($map);
        $flag = Db::table($this -> table)
            -> whereIn("project_level_id",$ids)
            -> delete();
        if($flag > 0){
            Db::table($this -> attributeTable)
                -> whereIn("project_level_id",$ids)
                -> delete();
            $this -> success("删除成功");
        }
        $this -> error("删除失败,该数据不存在");
    }
}