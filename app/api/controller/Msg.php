<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/22
 * Time: 15:02
 */

namespace app\api\controller;

use think\admin\Controller;
use think\facade\Cache;
use think\facade\Db;

class Msg extends Controller
{
    private $RemindersPrefix = "Reminders_";
    private $orders_table = "zp_orders";

    public function queryRemind(){
        $user = $this -> app -> session -> get("user");
        $result = array(
            "remind" => false
        );
        if($user){
            $flag = Cache::get($this -> RemindersPrefix.$user["id"]);
            $flag = empty($flag) ? true : false;
            if($flag){
                $count = Db::table($this -> orders_table)
                    -> where("JSON_CONTAINS(responsible_user_ids -> '$[*]',JSON_ARRAY({$user["id"]}),'$')")
                    -> count();
                if($count > 0){
                    $result["remind"] = true;
                }
            }
        }
        echo json_encode($result);die;
    }

    public function todayNoReminders(){
        $now_time = time();
        $user = $this -> app -> session -> get("user");
        if($user){
            $flag = Cache::get($this -> RemindersPrefix.$user["id"]);
            $flag = empty($flag) ? true : false;
            if($flag){
                $time_out = strtotime(date("Y-m-d",($now_time + 86400))) - $now_time;
                Cache::set($this -> RemindersPrefix.$user["id"],1,$time_out);
            }
        }
        die;
    }
}