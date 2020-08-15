<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/7
 * Time: 09:59
 */

namespace app\orders\controller;

use think\admin\Controller;
use think\facade\Db;

class Auth extends Controller
{
    protected $table = "zp_orders_auth";

    protected function _index_page_filter(&$data){
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    private function handlerData(array &$data,$display = true){
        if($display){
            if(isset($data["user_ids"])){
                $data["user_size"] = sizeof(json_decode($data["user_ids"],true));
            }
            if(isset($data["user_ids"])){
                $data["user_ids"] = json_decode($data["user_ids"],true);
            }
            if(isset($data["auth"])){
                $data["auth"] = json_decode($data["auth"],true);
            }
        }else{
            if(isset($data["orders_auth_id"])){
                unset($data["orders_auth_id"]);
            }
            if(isset($data["user_ids"])){
                $user_id_map = array();
                foreach ($data["user_ids"] as &$user_id){
                    $user_id = (int) $user_id;
                    if(empty($user_id_map[$user_id])){
                        $user_id_map[$user_id] = 1;
                    }else{
                        $this -> error("存在相同用户");
                    }
                }
                $data["user_ids"] = json_encode($data["user_ids"]);
            }
            if(isset($data["auth"])){
                $data["auth"] = json_encode(array_keys($data["auth"]));
            }
        }
    }
     /**
     * @auth true
     * @menu true
     */
    public function index(){
        $this -> title = "权限分组";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table);
        if(isset($params["auth_name"]) && strlen($params["auth_name"]) > 0){
            $query -> whereLike("auth_name","%{$params["auth_name"]}%");
        }
        $query -> field(array("orders_auth_id","auth_name","user_ids"))
            -> order("orders_auth_id","DESC")
            -> page(true, true, false, 0);
    }
    /**
     * @auth true
     * @menu true
     */
    public function add(){
        if($this -> request -> isGet()){
            $this -> fetch("form");
        }

        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            if(!isset($params["user_ids"])){
                $params["user_ids"] = "[]";
            }
            if(!isset($params["auth"])){
                $params["auth"] = "[]";
            }
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 
     * @auth true
     * @menu true
     */
    public function edit(int $orders_auth_id){
        if($this -> request -> isGet() && $orders_auth_id > 0){
            $vo = Db::table($this -> table)
                -> where("orders_auth_id",$orders_auth_id)
                -> field(array("orders_auth_id","auth_name","user_ids","auth"))
                -> find();
            $this -> handlerData($vo);
            $this -> fetch("form",array("vo" => $vo));
        }

        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table)
                -> where("orders_auth_id",$orders_auth_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("编辑失败,请刷新页面重试");
        }
    }

        /**
     * @auth true
     * @menu true
     */
    public function remove(int $orders_auth_id){
        if($this -> request -> isPost() && $orders_auth_id > 0){
            Db::table($this -> table)
                -> where("orders_auth_id",$orders_auth_id)
                -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
        }
    }
}