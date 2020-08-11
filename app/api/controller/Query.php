<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/17
 * Time: 09:24
 */

namespace app\api\controller;

use think\admin\Controller;
use think\facade\Db;

class Query extends Controller
{
    private $system_user_table = "system_user";
    private $orders_table = "zp_orders";
    private $project_attribute = "zp_project_attribute";
    private $orders_process = "zp_orders_process";

    public function ordersProcessArray(array $where = array(),array $whereIn = array(),array $field = array("orders_process_id","name")){
        $sql = Db::table($this -> orders_process);

        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }

        if(sizeof($whereIn) > 1){
            $sql = $sql -> whereIn($whereIn[0],json_decode($whereIn[1],true));
        }

        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }

        return json_encode($sql -> select() -> toArray());
    }

    public function systemUserArray(array $where = array(),array $whereIn = array(),array $field = array("id","username","nickname","headimg")){
        $sql = Db::table($this -> system_user_table);

        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }

        if(sizeof($whereIn) > 1){
            $sql = $sql -> whereIn($whereIn[0],json_decode($whereIn[1],true));
        }

        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }

        return json_encode($sql -> select() -> toArray());
    }

    public function ordersGraphical($days = 7){
        $days--;
        $now_time = time();
        $now_date = strtotime(date("Y-m-d",$now_time));
        $result = array(
            "title" => "项目订单",
            "legend" => array("项目","两化","知识产权","财务"),
            "xAxis" => array(),
            "series" => array(
                array("name" => "项目","type" => "bar","data" => array()),
                array("name" => "两化","type" => "bar","data" => array()),
                array("name" => "知识产权","type" => "bar","data" => array()),
                array("name" => "财务","type" => "bar","data" => array())
            )
        );
        foreach ($result["series"] as &$series){
            for($i = 0;$i <= $days;$i++){
                array_push($series["data"],0);
            }
        }
        $create_time = array();
        for($i = $days;$i >= 0;$i--){
            $create_time[$now_date - ($i * 86400)] = $days - $i;
            array_push($result["xAxis"],date("m-d",$now_date - ($i * 86400)));
        }
        $orders = Db::table($this -> orders_table)
            -> whereIn("create_date",array_keys($create_time))
            -> group("department,create_date")
            -> order("create_date","asc")
            -> field(array("department","create_date","count(orders_id) counts"))
            -> select() -> toArray();
        foreach ($orders as $key => $order){
            $index = $create_time[$order["create_date"]];
            switch ($order["department"]){
                case "PROJECT":
                    $result["series"][0]["data"][$index] = $order["counts"];
                    break;
                case "DICHOTOMY":
                    $result["series"][1]["data"][$index] = $order["counts"];
                    break;
                case "KNOWLEDGE":
                    $result["series"][2]["data"][$index] = $order["counts"];
                    break;
                case "FINANCE":
                    $result["series"][3]["data"][$index] = $order["counts"];
                    break;
            }
        }
        echo json_encode($result);die;
    }

    public function projectAttribute(array $where = array(),array $whereIn = array(),array $field = array("*")){
        $sql = Db::table($this -> project_attribute);

        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }

        if(sizeof($whereIn) > 1){
            $sql = $sql -> whereIn($whereIn[0],json_decode($whereIn[1],true));
        }

        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }

        return json_encode($sql -> select() -> toArray());
    }
}