<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/8
 * Time: 10:21
 */

namespace app\message\controller;

use think\admin\Controller;
use think\facade\Db;

class Index extends Controller
{
    protected $table = "zp_message_box";

    private function baseQuery($query,array $params = array()){
        $user = $this -> app -> session -> get("user");
        $query = $query -> alias("a")
            -> where("a.user_id",$user["id"]);
        if(!empty($params["message_box_id"])){
            $query = $query -> where("a.message_box_id",$params["message_box_id"]);
        }
        if(!empty($params["reader"])){
            $query = $query -> where("a.reader",$params["reader"]);
        }
        return $query;
    }

    private function handlerData(array &$data,bool $display = true){
        if($display){
            if(!empty($data["create_time"])){
                $data["create_time_text"] = date("Y-m-d H:i:s",$data["create_time"]);
            }
        }else{

        }
    }

    protected function _index_page_filter(&$data){
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    public function messageCount(){
        echo $this -> baseQuery(Db::table($this -> table),array("reader" => "N")) -> count();die;
    }

    public function readerAll(){
        $this -> baseQuery(Db::table($this -> table)) -> update(array("reader" => 'Y'));
        $this -> success("全部已读!");
    }

    public function listArray(){
        $this -> title = "未读消息盒";
        $list = $this -> baseQuery(Db::table($this -> table),array("reader" => "N"))
            -> field(array("a.message_box_id","a.title","a.reader","a.create_time"))
            -> order("a.message_box_id","DESC")
            -> select() -> toArray();
        $this -> _index_page_filter($list);
        $this -> fetch("list",array("list" => $list));
    }

    public function info(int $message_box_id){
        if($message_box_id > 0){
            $vo = $this -> baseQuery(Db::table($this -> table),array("message_box_id" => $message_box_id)) -> find();
            if($vo){
                $this -> baseQuery(Db::table($this -> table),array("message_box_id" => $message_box_id)) -> update(array("reader" => "Y"));
                $this -> fetch("",array("vo" => $vo));
            }
        }
    }

    public function index(){
        $this -> title = "消息盒";
        $this -> baseQuery($this -> _query($this -> table),$this -> request -> get())
            -> field(array("a.message_box_id","a.title","a.reader","a.create_date"))
            -> order("a.message_box_id","DESC")
            -> page(true, true, false, 0);
    }
}