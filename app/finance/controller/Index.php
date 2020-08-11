<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/18
 * Time: 17:16
 */

namespace app\finance\controller;

use think\admin\Controller;
use think\facade\Db;

/**
 * 财务管理
 * Class Index
 * @package app\finance\controller
 */
class Index extends Controller
{
    protected $table = "zp_finance";
    private $user_table = "system_user";

    protected function _index_page_filter(&$data){
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    private function handlerData(array &$data,bool $display = true){
        if($display){
            if(isset($data["state"])){
                switch ($data["state"]){
                    case "ORDER":
                        $data["state_text"] = "申请中";
                        break;
                    case "PASS":
                        $data["state_text"] = "通过,等待打款";
                        break;
                    case "COMPLETE":
                        $data["state_text"] = "已打款";
                        break;
                    case "REJECT":
                        $data["state_text"] = "拒绝";
                        break;
                    default:
                        $data["state_text"] = "";
                }
            }
            if(!empty($data["create_time"])){
                $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
            }
            if(!empty($data["update_time"])){
                $data["update_time"] = strtotime($data["update_time"]);
            }
        }else{
            if(isset($data["money"])){
                $data["money"] = round($data["money"],MONEY_ACCURACY);
            }
            if(isset($data["finance_id"])){
                unset($data["finance_id"]);
            }
        }
    }

    private function baseQuery($query,int $user_id,array $params = array()){
        $query = $query -> alias("a") -> join($this -> user_table." b","a.user_id = b.id");
        if(isset($params["username"]) && strlen($params["username"])){
            $query = $query -> whereLike("b.username","%{$params["username"]}%");
        }
        if(isset($params["state"]) && $params["state"] != "all"){
            $query = $query -> where("a.state",$params["state"]);
        }
        if(!empty($params["start_date"])){
            $query = $query -> where("a.create_date",">=",$params["start_date"]);
        }
        if(!empty($params["end_date"])){
            $query = $query -> where("a.create_date","<=",$params["end_date"]);
        }
        return $query;
    }

    /**
     * 申请列表
     * @menu true
     * @auth true
     */
    public function index(){
        $this -> title = "申请列表";
        $params = $this -> request -> get();
        $user = $this -> app -> session -> get("user");
        $this -> baseQuery($this -> _query($this -> table),$user["id"],$params)
            -> field(array("a.finance_id","a.title","a.content","a.type","a.type_content",
                "a.money","a.state","a.create_time","a.update_time","b.username","b.nickname","b.headimg"))
            -> order("a.finance_id","desc")
            -> page(true, true, false, 0);
    }

    /**
     * 发起申请
     * @menu true
     * @auth true
     */
    public function add(){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $this -> fetch("form");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $user = $this -> app -> session -> get("user");
            $params["user_id"] = $user["id"];
            $params["create_time"] = time();
            $params["create_date"] = strtotime(date("Y-m-d",$params["create_time"]));
            $this -> handlerData($params,false);
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("成功发起申请") : $this -> error("申请失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $finance_id
     * @throws \think\db\exception\DbException
     */
    public function edit($finance_id){
        $this -> _applyFormToken();
        $user = $this -> app -> session -> get("user");
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where(array("finance_id" => $finance_id,"user_id" => $user["id"])) -> find();
            if($vo === null || !is_array($vo) || sizeof($vo) <= 0){
                $this -> error("只允许发起人编辑");
            }
            if($vo["state"] != "ORDER"){
                $this -> error("已通过的记录无法进行编辑");
            }
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table) -> where(array("finance_id" => $finance_id,"user_id" => $user["id"]))
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 审核进度
     * @menu true
     * @auth true
     * @param $finance_id
     * @param $state
     * @param $update_time
     * @throws \think\db\exception\DbException
     */
    public function pass($finance_id,$state,$update_time){
        $where = array("finance_id" => $finance_id,"state" => "","update_time" => date("Y-m-d H:i:s",$update_time));
        switch ($state){
            case "PASS":
                $where["state"] = "ORDER";
                break;
            case "COMPLETE":
                $where["state"] = "PASS";
                break;
        }
        Db::table($this -> table) -> where($where)
            -> update(array("state" => $state)) > 0 ? $this -> success("已通过") : $this -> error("该申请记录发生变化,请刷新确认");
    }

    /**
     * 拒绝
     * @menu true
     * @auth true
     * @param $finance_id
     * @throws \think\db\exception\DbException
     */
    public function reject($finance_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $this -> fetch("reject");
        }
        if($this -> request -> isPost()){
            $reason = $this -> request -> post("reason");
            if(strlen($reason) <= 0){
                $reason = "";
            }
            Db::table($this -> table) -> where(array(array("finance_id","=",$finance_id),array("state","<>","COMPLETE")))
                -> update(array("state" => "REJECT","reason" => $reason)) > 0 ? $this -> success("已拒绝") : $this -> error("拒绝失败,请确认该记录是否已打款");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $finance_id
     * @throws \think\db\exception\DbException
     */
    public function remove($finance_id){
        $this -> _applyFormToken();
        Db::table($this -> table) -> where("finance_id",$finance_id)
            -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
    }
}