<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/8
 * Time: 09:22
 */

namespace app\patent\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\Exception;
use think\facade\Db;

/**
 * 专利管理
 * Class Index
 * @package app\patent\controller
 */
class Index extends Controller
{
    protected $table = "zp_patent";
    private $patent_pay_log_table = "zp_patent_pay_log";
    private $project_level_table = "zp_project_level";
    private $patent_annual_fee_table = "zp_patent_annual_fee";

    public function patentLevel(){
        return json_encode($this -> patentLevelTree());
    }

    private function patentLevelTree(){
        return DataExtend::arr2table(Db::table($this -> project_level_table)
            -> where("pid",1)
            -> field(array("project_level_id","pid","title"))
            -> order("project_level_id","asc")
            -> select() -> toArray(),"project_level_id");
    }

    private function handlerData(array &$data,bool $display = true){
        if($display){
            if(isset($data["apply_date"])){
                $data["apply_date_text"] = date("Y-m-d",$data["apply_date"]);
            }
            if(isset($data["warrant_date"])){
                $data["warrant_date_text"] = date("Y-m-d",$data["warrant_date"]);
            }
            if(isset($data["img_file_path"])){
                $data["img_file_path"] = json_decode($data["img_file_path"],true);
                foreach($data["img_file_path"] as &$vo){
                    $vo["preview_name"] = $vo["name"];
                    if(strlen($vo["preview_name"]) > 20){
                        $vo["preview_name"] = mb_substr($vo["preview_name"],0,10)."...";
                    }
//                    $vo["preview_path"] = $vo["path"];
//                    $suffix_name = explode(".",$vo["name"]);
//                    if(sizeof($suffix_name) > 1){
//                        $vo["preview_path"] = str_replace(".".$suffix_name[sizeof($suffix_name) - 1],
//                            ".".strtolower($suffix_name[sizeof($suffix_name) - 1]),$vo["path"]);
//                    }
//                    $vo["path"] = SERVER_NAME.$vo["path"];
//                    $vo["preview_path"] = SERVER_NAME.$vo["preview_path"];
                }
            }
//            if(!empty($data["pay"])){
//                switch ($data["pay"]){
//                    case "Y":
//                        $data["pay_text"] = "已缴费";
//                        break;
//                    default:
//                        $data["pay_red_text"] = "未缴费";
//                }
//            }
        }else{
            if(!empty($data["apply_date"])){
                $data["apply_date"] = strtotime($data["apply_date"]);
            }
            if(!empty($data["pay_end_date"])){
                $data["pay_end_date"] = strtotime($data["pay_end_date"]);
            }
            if(!empty($data["warrant_date"])){
                $data["warrant_date"] = strtotime($data["warrant_date"]);
            }
            if(isset($data["apply_date"]) && isset($data["pay_end_date"])){
                $data["annual_type"] = $data["pay_end_date"] - $data["apply_date"];
                if($data["annual_type"] <= 0){
                    $this -> error("未找到对应年费缴纳标准,请设置");
                }
                $data["annual_type"] = ceil($data["annual_type"] / 31536000);
                if(!empty($data["project_level_id"])){
                    $annual_fee = Db::table($this -> patent_annual_fee_table)
                        -> where("project_level_id",$data["project_level_id"])
                        -> where("`start_year` = {$data["annual_type"]} OR `end_year` = {$data["annual_type"]}")
                        -> field(array("annual_fee")) -> find();
                    if(is_array($annual_fee) && sizeof($annual_fee) > 0){
                        $data["annual_fee"] = $annual_fee["annual_fee"];
                        $data["reduce_annual_fee"] = $data["annual_fee"];
                        if(!empty($data["reduce"])){
                            $data["reduce_annual_fee"] = round($data["annual_fee"] - ($data["annual_fee"] * round($data["reduce"] / 100,MONEY_ACCURACY)),MONEY_ACCURACY);
                        }
                    }else{
                        $this -> error("未找到对应年费缴纳标准,请设置");
                    }
                }
            }
            if(isset($data["img_file_path"]) && is_array($data["img_file_path"])){
                $data["img_file_path"] = json_encode($data["img_file_path"]);
            }
        }
    }

    private function exporFilter(array &$data){
        $now_date = strtotime(date("Y-m-d"));
        $patent_map = array();
        foreach ($data as $key => &$vo){
            $this -> handlerData($vo);
            if(isset($vo["pay_date"]) && isset($vo["pay_end_date"])){
                $vo["pay_text"] = "未缴费";
                $vo["pay_end_date_class"] = "sub-span-red";
                if($vo["pay_end_date"] >= $now_date){
                    $vo["pay_text"] = "已缴费";
                    if($now_date >= strtotime("-2 month",$vo["pay_end_date"])){
                        $vo["pay_text"] .= "(预警)";
                        $vo["pay_end_date_class"] = "sub-span-red";
                    }else{
                        $vo["pay_end_date_class"] = "sub-span-blue";
                        $patent_map[$vo["patent_id"]] = $key;
                    }
                }
                $vo["pay_end_date_text"] = "";
                if(!empty($vo["pay_end_date"])){
                    $vo["pay_end_date_text"] = date("Y-m-d",$vo["pay_end_date"]);
                }
            }
        }
    }

    protected function _index_page_filter(&$data){
        $now_date = strtotime(date("Y-m-d"));
        $patent_map = array();
        foreach ($data as $key => &$vo){
            $this -> handlerData($vo);
            if(isset($vo["pay_date"]) && isset($vo["pay_end_date"])){
                $vo["pay_text"] = "未缴费";
                $vo["pay_end_date_class"] = "sub-span-red";
                if($vo["pay_end_date"] >= $now_date){
                    $vo["pay_text"] = "已缴费";
                    if($now_date >= strtotime("-2 month",$vo["pay_end_date"])){
                        $vo["pay_text"] .= "(预警)";
                        $vo["pay_end_date_class"] = "sub-span-red";
                    }else{
                        $vo["pay_end_date_class"] = "sub-span-blue";
                        $patent_map[$vo["patent_id"]] = $key;
                    }
                }
                $vo["pay_end_date_text"] = "";
                if(!empty($vo["pay_end_date"])){
                    $vo["pay_end_date_text"] = date("Y-m-d",$vo["pay_end_date"]);
                }
            }
        }
        if(sizeof($patent_map) > 0){
            $buildSql = Db::table($this -> patent_pay_log_table)
                -> whereIn("patent_id",array_keys($patent_map))
                -> field(array("max(patent_pay_log_id) patent_pay_log_id"))
                -> group("patent_id") -> buildSql();
            $pay_logs = Db::query("SELECT `patent_id`,`annual_type` FROM ".$this -> patent_pay_log_table." WHERE `patent_pay_log_id` IN {$buildSql} LIMIT ".sizeof($patent_map));
            foreach ($pay_logs as $pay_log){
                $index = $patent_map[$pay_log["patent_id"]];
                $pay_end_date = strtotime("-{$pay_log["annual_type"]} year",$data[$index]["pay_end_date"]);
                if($now_date >= strtotime("-2 month",$pay_end_date) && $now_date < $pay_end_date){
                    $data[$index]["pay_end_date_class"] = "sub-span-green";
                    $data[$index]["pay_end_date"] = $pay_end_date;
                    $data[$index]["pay_end_date_text"] = date("Y-m-d",$data[$index]["pay_end_date"]);
                }
            }
        }
    }

    private function baseQuery($query,array $params = array()){
        $sql = $query -> alias("a")
            -> leftJoin($this -> project_level_table." b","a.project_level_id = b.project_level_id");
        if(isset($params["patent_no"]) && strlen($params["patent_no"]) > 0){
            $sql = $sql -> where("a.patent_no",$params["patent_no"]);
        }
        if(isset($params["patent_name"]) && strlen($params["patent_name"]) > 0){
            $sql = $sql -> whereLike("a.patent_name","%{$params["patent_name"]}%");
        }
        if(isset($params["apply_people"]) && strlen($params["apply_people"]) > 0){
            $sql = $sql -> whereLike("a.apply_people","%{$params["apply_people"]}%");
        }
        if(!empty($params["apply_date"])){
            $apply_date = explode(" - ",$params["apply_date"]);
            if(sizeof($apply_date) > 1){
                $sql = $sql -> whereBetween("a.apply_date",array(strtotime($apply_date[0]),strtotime($apply_date[1])));
            }
        }
        if(!empty($params["pay_end_date"])){
            $pay_end_date = explode(" - ",$params["pay_end_date"]);
            if(sizeof($pay_end_date) > 1){
                $start_pay_date = strtotime($pay_end_date[0]);
                $end_pay_date = strtotime($pay_end_date[1]);
                $sql = $sql -> whereBetween("a.pay_end_date",array(strtotime("1970-".date("m-d",$start_pay_date)),strtotime("1970-".date("m-d",$end_pay_date))));
            }
        }
        if(isset($params["project_level_id"]) && $params["project_level_id"] != "all"){
            $sql = $sql -> where("a.project_level_id",$params["project_level_id"]);
        }
        return $sql;
    }

    /**
     * 专利列表
     * @menu true
     * @auth true
     */
    public function index(){
        $this -> title = "专利列表";
        $params = $this -> request -> get();
        $this -> baseQuery($this -> _query($this -> table),$params);
        $query = $this -> baseQuery($this -> _query($this -> table),$params);
        $query -> field(array("a.patent_id","a.patent_no","a.patent_name","a.apply_people",
            "a.apply_date","a.pay_end_date","a.pay_date","a.warrant_date","a.reduce",
            "a.annual_type","a.annual_fee","a.reduce_annual_fee","b.title"))
            -> order("a.patent_id","desc")
            -> page(true, true, false, 0);
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this->_applyFormToken();
        $now_time = time();
        if($this -> request -> isGet()){
            $this -> fetch("add");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            $params["pay_date"] = $params["apply_date"];
            if(empty($params["img_file_path"])){
                $params["img_file_path"] = "[]";
            }
            try{
                Db::startTrans();
                $patent_id = Db::table($this -> table) -> insertGetId($params);
                if($patent_id <= 0){
                    throw new Exception("patent_id <= 0");
                }
                $flag = Db::table($this -> patent_pay_log_table)
                    -> insert(array(
                        "patent_id" => $patent_id,
                        "year" => $params["annual_type"],
                        "reduce" => $params["reduce"],
                        "annual_type" => $params["annual_type"],
                        "annual_fee" => $params["annual_fee"],
                        "reduce_annual_fee" => $params["reduce_annual_fee"],
                        "create_date" => strtotime(date("Y-m-d",$now_time)),
                        "create_time" => $now_time
                    ));
                if($flag <= 0){
                    throw new Exception("flag <= 0");
                }
                Db::commit();
                $this -> success("添加成功");
            }catch (Exception $exception){
                if(empty($exception -> getMessage())){
                    $this -> success("添加成功");
                }
                Db::rollback();
            }
            $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $patent_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($patent_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("patent_id",$patent_id) -> find();
            $this -> handlerData($vo,true);
            $this -> fetch("edit",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            if(isset($params["patent_id"])){
                unset($params["patent_id"]);
            }
            Db::table($this -> table) -> where("patent_id",$patent_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 缴费
     * @menu true
     * @auth true
     * @param $patent_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pay($patent_id){
        $this->_applyFormToken();
        $now_time = time();
        $now_date = strtotime(date("Y-m-d",$now_time));
        $vo = Db::table($this -> table)
            -> where("patent_id",$patent_id)
            -> field(array("patent_id","project_level_id","pay_end_date","reduce","annual_type")) -> find();
        if(!$vo){
            $this -> error("该专利不存在");
        }
        if($this -> request -> isGet()){
            $start_red_date = strtotime("-2 month",$vo["pay_end_date"]);
            if($now_date < $start_red_date){
                $this -> error("该专利未预警或逾期");
            }
            $this -> handlerData($vo,true);
            $this -> fetch("pay",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $params["create_time"] = $now_time;
            $params["create_date"] = $now_date;
            $params["annual_type"] = $params["year"];
            $annual_fee = Db::table($this -> patent_annual_fee_table)
                -> where("project_level_id",$params["project_level_id"])
                -> where("`start_year` = {$params["year"]} OR `end_year` = {$params["year"]}")
                -> field(array("annual_fee")) -> find();
            if(is_array($annual_fee) && sizeof($annual_fee) > 0){
                $params["annual_fee"] = $annual_fee["annual_fee"];
                $params["reduce_annual_fee"] = $params["annual_fee"];
                if(!empty($params["reduce"])){
                    $params["reduce_annual_fee"] = round($params["annual_fee"] - ($params["annual_fee"] * round($params["reduce"] / 100,MONEY_ACCURACY)),MONEY_ACCURACY);
                }
            }
            if(isset($params["project_level_id"])){
                unset($params["project_level_id"]);
            }
            try{
                $insert_flag = Db::table($this -> patent_pay_log_table) -> insert($params);
                if($insert_flag <= 0){
                    throw new Exception("insert_flag <= 0");
                }
                $update = array(
                    "pay_date" => $now_date,
                    "pay_end_date" => strtotime("+{$params["annual_type"]} year",$now_date)
                );
                if($vo["pay_end_date"] >= $now_date){
                    $update["pay_end_date"] = strtotime("+{$params["annual_type"]} year",$vo["pay_end_date"]);
                }
                $update_flag = Db::table($this -> table)
                    -> where("patent_id",$patent_id)
                    -> update($update);
                if($update_flag <= 0){
                    throw new Exception("update_flag <= 0");
                }
                Db::commit();
                $this -> success("缴费成功");
            }catch (Exception $exception){
                if(empty($exception -> getMessage())){
                    $this -> success("缴费成功");
                }
                Db::rollback();
            }
            $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $patent_id
     * @throws \think\db\exception\DbException
     */
    public function remove($patent_id){
        $this->_applyFormToken();
        Db::table($this -> table) -> where("patent_id",$patent_id)
            -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
    }

    /**
     * 文件列表
     * @menu false
     * @auth false
     * @param $patent_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function files($patent_id){
        if($this -> request -> isGet()){
            $files = array();
            $vo = Db::table($this -> table) -> where("patent_id",$patent_id) -> field(array("img_file_path")) -> find();
            if(is_array($vo) && sizeof($vo) > 0 && !empty($vo["img_file_path"])){
                $files = json_decode($vo["img_file_path"],true);
            }
            $this -> fetch("files",array("files" => $files));
        }
    }

    /**
     * 导出Excel表
     * @menu true
     * @auth true
     */
    public function exportExcel(){
        $params = $this -> request -> get();
        $filename = "专利列表";
        $field = array("a.patent_id","a.patent_no","a.patent_name","a.apply_people",
            "a.apply_date","a.pay_end_date","a.pay_date","a.warrant_date","a.reduce",
            "a.annual_type","a.annual_fee","a.reduce_annual_fee","b.title");
        $field_text = array("类型","专利号","专利名称","申请人","申请日","授权日","缴费截止日期","费减比例","费用种类","年费金额","费减后年费金额","是否缴费");
        ini_set('memory_limit','2048M'); //设置程序运行的内存
        ini_set('max_execution_time',0); //设置程序的执行时间,0为无上限
        ob_end_clean();  //清除内存
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=".$filename.'.csv');
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fp,$field_text);
        $number = 0;
        $limit = 1000;
        $count = $this -> baseQuery(Db::table($this -> table),$params) -> count();
        while($count > 0){
            $count -= $limit;
            $query = $this -> baseQuery(Db::table($this -> table),$params);
            $data = $query -> field($field)
                -> limit($number++ * $limit,$limit)
                -> order("a.patent_id","DESC")
                -> select() -> toArray();
            $this -> exporFilter($data,true);
            $index = 0;
            foreach ($data as $item) {
                if($index == $limit){ //每次写入1000条数据清除内存
                    $index = 0;
                    ob_flush();//清除内存
                    flush();
                }
                $index++;
                fputcsv($fp,array($item["title"],$item["patent_no"],$item["patent_name"],$item["apply_people"],$item["apply_date_text"],$item["warrant_date_text"],
                    $item["pay_end_date_text"],"{$item["reduce"]}%","第{$item["annual_type"]}年年费",$item["annual_fee"],$item["reduce_annual_fee"],$item["pay_text"]));
            }
            ob_flush();
            flush();
        }
        ob_end_clean();
        exit();
    }
}