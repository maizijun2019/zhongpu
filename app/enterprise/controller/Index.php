<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/11
 * Time: 16:10
 */

namespace app\enterprise\controller;

use Exception;
use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 企业管理
 * Class Index
 * @package app\enterprise\controller
 */
class Index extends Controller
{
    protected $table = "zp_enterprise";
    private $industry_table = "zp_industry";
    private $region_table = "zp_region";
    private $consumer_table = "zp_consumer";

    public function industryArray(){
        return json_encode($this -> industryTree(array(),array("industry_id","pid","industry_name")));
    }

    private function subIndustryAll(int $pid){
        $industry_ids = array($pid);
        $tree = $this -> industryTree(array("pid" => $pid),array("industry_id","pid"));
        foreach ($tree as $vo){
            array_push($industry_ids,$vo["industry_id"]);
        }
        return $industry_ids;
    }

    private function industryTree(array $where = array(),array $field = array()){
        $tree = Db::table($this -> industry_table);
        if(sizeof($where) > 0){
            $tree = $tree -> where($where);
        }
        if(sizeof($field) > 0){
            $tree = $tree -> field($field);
        }
        $tree = $tree -> order("industry_id","asc")
            -> select() -> toArray();
        return DataExtend::arr2table($tree,"industry_id");
    }

    public function consumerArray(array $where = array(),array $field = array("consumer_id","username")){
        $consumers = Db::table($this -> consumer_table);
        if(sizeof($where) > 0){
            $consumers = $consumers -> where($where);
        }
        if(sizeof($field) > 0){
            $consumers = $consumers -> field($field);
        }
        return json_encode($consumers -> order("consumer_id","desc") -> select() -> toArray());
    }

    protected function _index_page_filter(&$data){
        $industry_ids = array();
        $industry_map = array();
        $address_ids = array();
        $address_map = array("province" => array(),"city" => array(),"area" => array());
        for($i = 0;$i < sizeof($data);$i++){
            $this -> handlerData($data[$i]);

            $industry_ids[$data[$i]["industry_id"]] = true;
            if(empty($industry_map[$data[$i]["industry_id"]])){
                $industry_map[$data[$i]["industry_id"]] = array($i);
            }else{
                array_push($industry_map[$data[$i]["industry_id"]],$i);
            }
            unset($data[$i]["industry_id"]);

            $address_ids[$data[$i]["province_region_id"]] = true;
            $address_ids[$data[$i]["city_region_id"]] = true;
            $address_ids[$data[$i]["area_region_id"]] = true;
            if(empty($address_map["province"][$data[$i]["province_region_id"]])){
                $address_map["province"][$data[$i]["province_region_id"]] = array($i);
            }else{
                array_push($address_map["province"][$data[$i]["province_region_id"]],$i);
            }
            unset($data[$i]["province_region_id"]);
            if(empty($address_map["city"][$data[$i]["city_region_id"]])){
                $address_map["city"][$data[$i]["city_region_id"]] = array($i);
            }else{
                array_push($address_map["city"][$data[$i]["city_region_id"]],$i);
            }
            unset($data[$i]["city_region_id"]);
            if(empty($address_map["area"][$data[$i]["area_region_id"]])){
                $address_map["area"][$data[$i]["area_region_id"]] = array($i);
            }else{
                array_push($address_map["area"][$data[$i]["area_region_id"]],$i);
            }
            unset($data[$i]["area_region_id"]);
        }

        if(sizeof($industry_ids) > 0){
            $industry_array = Db::table($this -> industry_table)
                -> whereIn("industry_id",array_keys($industry_ids))
                -> field(array("industry_id","industry_name"))
                -> limit(sizeof($industry_ids)) -> select();
            foreach($industry_array as $vo){
                $indexs = $industry_map[$vo["industry_id"]];
                foreach ($indexs as $index){
                    $data[$index]["industry_id_text"] = $vo["industry_name"];
                }
            }
        }

        if(sizeof($address_ids) > 0){
            $regions = Db::table($this -> region_table)
                -> whereIn("region_id",array_keys($address_ids))
                -> field(array("region_id","name","level"))
                -> limit(sizeof($address_ids))
                -> select() -> toArray();
            foreach ($regions as $region){
                switch ($region["level"]){
                    case 1:
                        foreach ($address_map["province"] as $province){
                            foreach ($province as $index){
                                $data[$index]["province_region_id_text"] = $region["name"];
                            }
                        }
                        break;
                    case 2:
                        foreach ($address_map["city"] as $city){
                            foreach ($city as $index){
                                $data[$index]["city_region_id_text"] = $region["name"];
                            }
                        }
                        break;
                    case 3:
                        foreach ($address_map["area"] as $area){
                            foreach ($area as $index){
                                $data[$index]["area_region_id_text"] = $region["name"];
                            }
                        }
                        break;
                }
            }
        }
    }

    private function handlerData(array &$data,bool $display = true){
        if($display){

        }else{
            if(isset($data["industry_id"])){
                $count = Db::table($this -> industry_table) -> where("pid",$data["industry_id"]) -> count();
                if($count > 0){
                    $this -> error("请选择最下级行业");
                }
            }
            if(isset($data["industry_id"]) && $data["industry_id"] <= 0){
                $this -> error("请选择行业");
            }
            if(isset($data["province_region_id"]) && $data["province_region_id"] <= 0){
                $this -> error("请选择省");
            }
            if(isset($data["city_region_id"]) && $data["city_region_id"] <= 0){
                $this -> error("请选择市");
            }
            if(isset($data["area_region_id"]) && $data["area_region_id"] <= 0){
                $this -> error("请选择区");
            }
            if(isset($data["enterprise_id"])){
                unset($data["enterprise_id"]);
            }
        }
    }


    /**
     * 企业列表
     * @menu true
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "企业列表";
        $query = $this -> _query($this -> table) -> alias("a")
            -> join($this -> consumer_table . " b","a.consumer_id = b.consumer_id");
        $params = $this -> request -> get();
        if(isset($params["username"]) && strlen($params["username"]) > 0){
            $query -> whereLike("b.username","%{$params["username"]}%");
        }
        if(isset($params["enterprise_name"]) && strlen($params["enterprise_name"]) > 0){
            $query -> whereLike("a.enterprise_name","%{$params["enterprise_name"]}%");
        }
        if(isset($params["industry_id"]) && $params["industry_id"] != "all"){
            $industry_ids = $this -> subIndustryAll($params["industry_id"]);
            $query -> whereIn("industry_id",$industry_ids);
        }
        if(isset($params["province_region_id"]) && $params["province_region_id"] != "all"){
            $query -> where("province_region_id",$params["province_region_id"]);
        }
        if(isset($params["city_region_id"]) && $params["city_region_id"] != "all"){
            $query -> where("city_region_id",$params["city_region_id"]);
        }
        if(isset($params["area_region_id"]) && $params["area_region_id"] != "all"){
            $query -> where("area_region_id",$params["area_region_id"]);
        }
        $query -> field(array("a.enterprise_id","b.username","a.enterprise_name","a.contacts","a.phone","a.fixed_telephone",
            "a.industry_id","a.province_region_id","a.city_region_id","a.area_region_id","a.address"))
            -> order("a.enterprise_id","desc")
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
            $this -> fetch("form");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            $params["attribute"] = "{}";
            try{
                Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
            }catch (Exception $exception){
                if(strpos($exception -> getMessage(),"SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$params["consumer_id"]}' for key 'consumer_id'") !== false){
                    $this -> error("该用户已添加过企业");
                }
            }
            $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $enterprise_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($enterprise_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("enterprise_id",$enterprise_id) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table)
                -> where("enterprise_id",$enterprise_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $enterprise_id
     * @throws \think\db\exception\DbException
     */
    public function remove($enterprise_id){
        $this -> _applyFormToken();
        Db::table($this -> table)
            -> where("enterprise_id",$enterprise_id)
            -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
    }
}