<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/3
 * Time: 16:03
 */

namespace app\region\controller;

use think\admin\Controller;
use think\facade\Db;

class Index extends Controller
{
    protected $table = "zp_region";

    public function province(int $province_region_id = 0){
        $data = Db::table($this -> table) -> where("level",1);
        if($province_region_id > 0){
            $data = $data -> where("region_id",$province_region_id);
        }
        return json_encode($data -> field(array("region_id","name")) -> select());
    }

    public function city(int $province_region_id){
        return json_encode(Db::table($this -> table)
            -> where(array("level" => 2,"region_pid" => $province_region_id))
            -> field(array("region_id","name")) -> select());
    }

    public function area(int $city_region_id){
        return json_encode(Db::table($this -> table)
            -> where(array("level" => 3,"region_pid" => $city_region_id))
            -> field(array("region_id","name")) -> select());
    }
}