<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/18
 * Time: 16:31
 */

namespace app\orders\controller;

use think\admin\Controller;
use think\facade\Db;

class Process extends Controller
{
    protected $table = "zp_orders_process";
    private $project_level_type = "zp_project_level_type";

    private function baseQuery($query,array $params = array()){
        $query = $query -> alias("a");
        if(!empty($params["orders_process_id"])){
            $query = $query -> where("a.orders_process_id",$params["orders_process_id"]);
        }
        return $query;
    }

    private function handlerData(array &$data,$display = true){
        if($display){

        }else{
            if(isset($data["state"]) && is_array($data["state"])){
                foreach ($data["state"] as &$state){
                    if(isset($state["auth"]) && is_array($state["auth"])){
                        $state["auth"] = array_keys($state["auth"]);
                    }else{
                        $state["auth"] = array();
                    }
                }
                $data["state"] = json_encode($data["state"]);
            }
        }
    }

    public function index(){
        $this -> title = "流程列表";
        $params = $this -> request -> get();
        $this -> baseQuery($this -> _query($this -> table),$params)
            -> field(array("a.orders_process_id","a.name","a.describe"))
            -> order("a.orders_process_id","DESC")
            -> page(true, true, false, 0);
    }

    public function add(){
        if($this -> request -> isGet()){
            $this -> fetch();
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    public function info(int $orders_process_id){

    }

    public function remove(int $orders_process_id){
        if($this -> request -> isPost()){
            if(Db::table($this -> project_level_type) -> where("orders_process_id",$orders_process_id) -> count() > 0){
                $this -> error("该流程尚有分类使用中");
            }
            Db::table($this -> table)
                -> where("orders_process_id",$orders_process_id)
                -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
        }
    }
}