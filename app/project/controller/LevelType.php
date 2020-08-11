<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/10
 * Time: 16:30
 */

namespace app\project\controller;

use think\admin\Controller;
use think\facade\Db;

class LevelType extends Controller
{
    protected $table = "zp_project_level_type";
    private $orders_process_table = "zp_orders_process";

    private function baseQuery($query,array $params = array()){
        $query = $query -> alias("a")
            -> leftJoin($this -> orders_process_table." b","a.orders_process_id = b.orders_process_id");
        if(!empty($params["project_level_type_id"])){
            $query = $query -> where("project_level_type_id",$params["project_level_type_id"]);
        }
        if(isset($params["name"]) && strlen($params["name"]) > 0){
            $query = $query -> where("name",$params["name"]);
        }
        return $query;
    }

    private function handlerData(array $data,$display = true){
        if($display){

        }else{
            if(isset($data["project_level_type_id"])){
                unset($data["project_level_type_id"]);
            }
        }
    }

    public function index(){
        $this -> title = "等级分类";
        $this -> baseQuery($this -> _query($this -> table),$this -> request -> get())
            -> order("a.project_level_type_id","DESC")
            -> field(array("a.*","b.name process_name"))
            -> page(false,true,false);
    }

    public function add(){
        if($this -> request -> isGet()){
            $this -> fetch("form");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    public function edit(int $project_level_type_id){
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("project_level_type_id",$project_level_type_id) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table)
                -> where("project_level_type_id",$project_level_type_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("编辑失败,请刷新页面重试");
        }
    }

    public function remove(int $project_level_type_id){
        if($this -> request -> isPost()){
            Db::table($this -> table)
                -> where("project_level_type_id",$project_level_type_id)
                -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
        }
    }
}