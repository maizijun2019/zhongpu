<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/11
 * Time: 15:22
 */

namespace app\industry\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 行业管理
 * Class Index
 * @package app\industry\controller
 */
class Index extends Controller
{
    protected $table = "zp_industry";

    protected function _index_page_filter(&$data){
        $data = DataExtend::arr2table($data,"industry_id");
    }

    public function industryArray(){
        return json_encode($this -> industryTree());
    }

    private function subIndustry(int $industry_id){
        $industry_ids = array();
        if($industry_id > 0){
            $industry_ids[$industry_id] = 1;
            $tree = DataExtend::arr2table($this -> industryTree(array(array("industry_id",">",$industry_id))),"industry_id");
            foreach ($tree as $vo){
                if(!empty($industry_ids[$vo["pid"]])){
                    array_push($industry_ids,array($vo["industry_id"] => 1));
                }
            }
        }
        return array_keys($industry_ids);
    }

    private function industryTree(array $where = array(),array $field = array("industry_id","pid","industry_name")){
        $tree = Db::table($this -> table);
        if(sizeof($where) > 0){
            $tree = $tree -> where($where);
        }
        if(sizeof($field) > 0){
            $tree = $tree -> field($field);
        }
        return DataExtend::arr2table($tree -> order("industry_id","asc") -> select() -> toArray(),"industry_id");
    }

    /**
     * 行业列表
     * @menu true
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "行业列表";
        $query = $this -> _query($this -> table);
        $params = $this -> request -> get();
        if(isset($params["industry_id"]) && $params["industry_id"] != "all"){
            $query -> whereIn("industry_id",$this -> subIndustry($params["industry_id"]));
        }
        $query -> order("industry_id","asc") -> page(false, true,false);
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
            if($pid > 0){
                $count = Db::table($this -> table) -> where("industry_id",$pid) -> sum("pid");
                if($count > 0){
                    $this -> error("当前已是最后一级");
                }
            }
            $this -> fetch("form",array(
                "tree" => $this -> industryTree(array("pid" => 0)),
                "pid" => $pid
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $industry_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($industry_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $tree = $this -> industryTree(array("pid" => 0));
            $vo = Db::table($this -> table) -> where("industry_id",$industry_id) -> find();
            $this -> fetch("form",array(
                "tree" => $tree,
                "vo" => $vo,
                "pid" => $vo["pid"]
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(!empty($params["industry_id"])){
                unset($params["industry_id"]);
            }
            Db::table($this -> table) -> where("industry_id",$industry_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $industry_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function remove($industry_id){
        $map = array($industry_id => 1);
        $tree = DataExtend::arr2table(Db::table($this -> table) -> where("industry_id",">",$industry_id) -> field(array("industry_id","pid")) -> order("industry_id","asc") -> select() -> toArray(),"industry_id");
        foreach ($tree as $vo){
            if(!empty($map[$vo["pid"]])){
                array_push($map,array($vo["industry_id"] => 1));
            }
        }
        $ids = array_keys($map);
        $flag = Db::table($this -> table)
            -> whereIn("industry_id",$ids)
            -> delete();
        if($flag > 0){
            $this -> success("删除成功");
        }
        $this -> error("删除失败,该数据不存在");
    }
}