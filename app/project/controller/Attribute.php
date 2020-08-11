<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/3
 * Time: 14:19
 */

namespace app\project\controller;

use think\admin\Controller;
use think\facade\Db;

/**
 * 数据属性
 * Class Attribute
 * @package app\project\controller
 */
class Attribute extends Controller
{
    protected $table = "zp_project_attribute";

    protected function _index_page_filter(&$data){
        foreach ($data as $key => &$vo){
            $this -> handlerData($vo,true);
        }
    }

    private function handlerData(array &$data,bool $display = false){
        if($display){
            if(isset($data["condition"])){
                $data["condition_text"] = "";
                switch ($data["condition"]){
                    case "==":
                        $data["condition_text"] = "等于";
                        break;
                    case ">":
                        $data["condition_text"] = "大于";
                        break;
                    case "<":
                        $data["condition_text"] = "小于";
                        break;
                    case ">=":
                        $data["condition_text"] = "大于等于";
                        break;
                    case "<=":
                        $data["condition_text"] = "小于等于";
                        break;
                }
                unset($data["condition"]);
            }
            if(isset($data["display"])){
                switch ($data["display"]){
                    case "Y":
                        $data["display_text"] = "是";
                        break;
                    default:
                        $data["display_text"] = "否";

                }
                unset($data["display"]);
            }
            if(isset($data["required"])){
                switch ($data["required"]){
                    case "Y":
                        $data["required_text"] = "是";
                        break;
                    default:
                        $data["required_text"] = "否";
                }
                unset($data["required"]);
            }
        }else{
            if(!empty($data["project_attribute_id"])){
                unset($data["project_attribute_id"]);
            }
        }
    }

    /**
     * 显示列表
     * @menu
     * @auth
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "属性管理";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table) -> alias("a");
        if(isset($params["attribute_name"]) && strlen($params["attribute_name"]) > 0){
            $query -> whereLike("a.attribute_name","%{$params["attribute_name"]}%");
        }
        $query -> field(array("a.project_attribute_id","a.attribute_name","a.company","a.condition","a.display","a.required","a.remarks"))
            -> order("a.project_attribute_id","desc")
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
            $this -> fetch("form");
        }

        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params);
            if(!isset($params["remarks"]) || strlen($params["remarks"]) <= 0){
                $params["remarks"] = "";
            }
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $project_attribute_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($project_attribute_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("project_attribute_id",$project_attribute_id) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params);
            Db::table($this -> table) -> where("project_attribute_id",$project_attribute_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $project_attribute_id
     * @throws \think\db\exception\DbException
     */
    public function remove($project_attribute_id){
        $this->_applyFormToken();
        Db::table($this -> table) -> where("project_attribute_id",$project_attribute_id) -> delete() > 0 ? $this -> success("删除成功") : $this -> error("删除失败,该数据不存在");
    }
}