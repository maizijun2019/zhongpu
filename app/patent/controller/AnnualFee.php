<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/8
 * Time: 10:56
 */

namespace app\patent\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 年费缴纳标准
 * Class AnnualFee
 * @package app\patent\controller
 */
class AnnualFee extends Controller
{
    protected $table = "zp_patent_annual_fee";
    private $project_level_table = "zp_project_level";

    public function patentLevel(){
        return json_encode($this -> patentLevelTree());
    }

    private function patentLevelTree(){
        return DataExtend::arr2table(Db::table($this -> project_level_table)
            -> where("pid",1)
            -> field(array("project_level_id","pid","title"))
            -> order("project_level_id","asc")
            -> select() -> toArray(),"project_level_id");
    }

    /**
     * 列表
     * @menu true
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "年费缴纳标准";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table) -> alias("a")
            -> leftJoin($this -> project_level_table . " b","a.project_level_id = b.project_level_id");
        if(isset($params["project_level_id"]) && $params["project_level_id"] != "all"){
            $query -> where("a.project_level_id",$params["project_level_id"]);
        }
        if(!empty($params["start_year"])){
            $query -> where("start_year",$params["start_year"]);
        }
        if(!empty($params["end_year"])){
            $query -> where("end_year",$params["end_year"]);
        }
        $query -> field(array("a.patent_annual_fee_id","a.project_level_id","a.start_year","a.end_year","a.annual_fee","b.title"))
            -> order("a.patent_annual_fee_id","asc")
            -> page(true, true, false, 0);
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $this -> fetch("add");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(empty($params["annual_fee"]) || sizeof(explode(".",$params["annual_fee"])) > 2){
                $this -> error("请填写正确的年费");
            }
            if(empty($params["start_year"]) || empty($params["end_year"])
            || $params["start_year"] <= 0 || $params["end_year"] <= 0
            || $params["start_year"] > $params["end_year"]){
                $this -> error("请填写正确的年区间");
            }
            $flag = Db::execute("INSERT INTO ".$this -> table." (`project_level_id`,`start_year`,`end_year`,`annual_fee`)
                SELECT {$params["project_level_id"]},{$params["start_year"]},{$params["end_year"]},{$params["annual_fee"]} FROM DUAL
                WHERE NOT EXISTS (SELECT `patent_annual_fee_id` FROM ".$this -> table."
                WHERE `project_level_id` = {$params["project_level_id"]}
                AND `start_year` = {$params["start_year"]}
                AND `end_year` = {$params["end_year"]});");
            if($flag <= 0){
                $this -> error("该分类下已有此年区间");
            }
            $this -> success("添加成功");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $patent_annual_fee_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($patent_annual_fee_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("patent_annual_fee_id",$patent_annual_fee_id) -> find();
            $this -> fetch("edit",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(empty($params["annual_fee"]) || sizeof(explode(".",$params["annual_fee"])) > 2){
                $this -> error("请填写正确的年费");
            }
            if(empty($params["start_year"]) || empty($params["end_year"])
                || $params["start_year"] <= 0 || $params["end_year"] <= 0
                || $params["start_year"] > $params["end_year"]){
                $this -> error("请填写正确的年区间");
            }
            if(isset($params["patent_annual_fee_id"])){
                unset($params["patent_annual_fee_id"]);
            }
            $count = Db::table($this -> table)
                -> where(array("project_level_id" => $params["project_level_id"],
                    "start_year" => $params["start_year"],"end_year" => $params["end_year"])) -> count();
            if($count > 0){
                $this -> error("该分类下已有此年区间");
            }
            Db::table($this -> table) -> where("patent_annual_fee_id",$patent_annual_fee_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("编辑失败,没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $patent_annual_fee_id
     * @throws \think\db\exception\DbException
     */
    public function remove($patent_annual_fee_id){
        $this -> _applyFormToken();
        Db::table($this -> table) -> where("patent_annual_fee_id",$patent_annual_fee_id)
            -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
    }
}