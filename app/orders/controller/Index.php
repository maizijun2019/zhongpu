<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/10
 * Time: 13:41
 */

namespace app\orders\controller;

use Exception;
use http\Params;
use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;
use app\orders\model\Order;
use app\orders\model\User;
use app\orders\model\ApprovalLog;
use app\orders\model\Project;
use app\orders\model\Enterprise;
use think\Request;
use app\validate\OrderValidate;
use think\exception\ValidateException;

/**
 * 项目管理
 * Class Index
 * @package app\orders\controller
 */
class Index extends Controller
{
    protected $table = "zp_orders";
    private $orders_process_table = "zp_orders_process";
    private $orders_settlement_table = "zp_orders_settlement";
    private $orders_reject_log_table = "zp_orders_reject_log";
    private $user_table = "system_user";
    private $project_table = "zp_project";
    private $enterprise_table = "zp_enterprise";
    private $region_table = "zp_region";
    private $project_level_table = "zp_project_level";
    private $system_user_table = "system_user";

    private $needReviewProcesses = ['公示', '结果公示']; 

    public function projectArray(array $where = array(),array $field = array("project_id","project_name")){
        return json_encode($this -> project($where,$field));
    }

    public function enterpriseArray(array $where = array(),array $field = array("enterprise_id","enterprise_name")){
        return json_encode($this -> enterprise($where,$field));
    }

    public function systemUserArray(array $where = array(),array $field = array("id","username","nickname","headimg")){
        return json_encode($this -> systemUser($where,$field));
    }

    private function project(array $where = array(),array $field = array()){
        $sql = Db::table($this -> project_table);
        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        return $sql -> select() -> toArray();
    }

    private function enterprise(array $where = array(),array $field = array()){
        $sql = Db::table($this -> enterprise_table);
        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        return $sql -> select() -> toArray();
    }

    private function systemUser(array $where = array(),array $field = array()){
        $sql = Db::table($this -> system_user_table);
        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        return $sql -> select() -> toArray();
    }

    private function findNextState(string $department,string $state){
        $orders_process_id = Db::table($this -> orders_process_table)
            -> where(array("department" => $department,"state" => $state))
            -> field(array("orders_process_id")) -> find();
        if($orders_process_id){
            $state = Db::table($this -> orders_process_table)
                -> where(array("department" => $department))
                -> where("orders_process_id",">",$orders_process_id["orders_process_id"])
                -> field(array("state")) -> order("orders_process_id","asc") -> find();
            if($state){
                return $state["state"];
            }
        }
        return "";
    }

    protected function _index_page_filter(&$data){
        $user_ids = array();
        $user_map = array("receiving_user_id" => array(),"approval_user_id" => array());
        $enterprise_ids = array();
        $enterprise_map = array();
        $address_ids = array();
        $address_map = array("province" => array(),"city" => array(),"area" => array());
        $level_ids = array();
        for($i = 0;$i < sizeof($data);$i++){
            $this -> handlerData($data[$i]);

            if(isset($data[$i]["project_level_id"])){
                if(isset($level_ids[$data[$i]["project_level_id"]])){
                    array_push($level_ids[$data[$i]["project_level_id"]],$i);
                }else{
                    $level_ids[$data[$i]["project_level_id"]] = array($i);
                }
                unset($data[$i]["project_level_id"]);
            }

            if(!empty($data[$i]["province_region_id"]) && !empty($data[$i]["city_region_id"]) && !empty($data[$i]["area_region_id"])){
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

            if(!empty($data[$i]["receiving_user_id"])){
                if(empty($user_map["receiving_user_id"][$data[$i]["receiving_user_id"]])){
                    $user_map["receiving_user_id"][$data[$i]["receiving_user_id"]] = array($i);
                }else{
                    array_push($user_map["receiving_user_id"][$data[$i]["receiving_user_id"]],$i);
                }
                array_push($user_ids,$data[$i]["receiving_user_id"]);
            }
            if(!empty($data[$i]["responsible_user_ids"])){
                $data[$i]["responsible_user_ids"] = json_decode($data[$i]["responsible_user_ids"],true);
                $data[$i]["responsible_size"] = sizeof($data[$i]["responsible_user_ids"]);
            }
            if(!empty($data[$i]["approval_user_id"])){
                if(empty($user_map["approval_user_id"][$data[$i]["approval_user_id"]])){
                    $user_map["approval_user_id"][$data[$i]["approval_user_id"]] = array($i);
                }else{
                    array_push($user_map["approval_user_id"][$data[$i]["approval_user_id"]],$i);
                }
                array_push($user_ids,$data[$i]["approval_user_id"]);
            }
            if(!empty($data[$i]["enterprise_id"])){
                if(empty($enterprise_map[$data[$i]["enterprise_id"]])){
                    $enterprise_map[$data[$i]["enterprise_id"]] = array($i);
                }else{
                    array_push($enterprise_map[$data[$i]["enterprise_id"]],$i);
                }
                array_push($enterprise_ids,$data[$i]["enterprise_id"]);
            }
        }

        if(sizeof($level_ids) > 0){
            $levels = Db::table($this -> project_level_table) -> alias("a")
                -> join($this -> project_level_table." b","a.pid = b.project_level_id")
                -> whereIn("a.project_level_id",array_keys($level_ids))
                -> field(array("a.project_level_id","b.title")) -> limit(sizeof($level_ids))
                -> select() -> toArray();
            foreach ($levels as $level){
                $indexs = $level_ids[$level["project_level_id"]];
                foreach ($indexs as $index){
                    $data[$index]["level_name"] = $level["title"];
                }
            }
        }

        if(sizeof($user_ids) > 0){
            $users = Db::table($this -> user_table)
                -> whereIn("id",$user_ids)
                -> limit(sizeof($user_ids))
                -> field(array("id","username","nickname","headimg")) -> select();
            foreach ($users as $vo){
                if(!empty($user_map["receiving_user_id"][$vo["id"]])){
                    $indexs = $user_map["receiving_user_id"][$vo["id"]];
                    foreach ($indexs as $index){
                        $data[$index]["receiving_user_id_text"] = "{$vo["username"]} : {$vo["nickname"]}";
                        $data[$index]["receiving_headimg"] = $vo["headimg"];
                    }
                }
                if(!empty($user_map["approval_user_id"][$vo["id"]])){
                    $indexs = $user_map["approval_user_id"][$vo["id"]];
                    foreach ($indexs as $index){
                        $data[$index]["approval_user_id_text"] = "{$vo["username"]} : {$vo["nickname"]}";
                        $data[$index]["approval_headimg"] = $vo["headimg"];
                    }
                }
            }
        }

        if(sizeof($enterprise_ids) > 0){
            $enterprises = Db::table($this -> enterprise_table)
                -> whereIn("enterprise_id",$enterprise_ids)
                -> field(array("enterprise_id","enterprise_name","contacts","phone","fixed_telephone")) -> limit(sizeof($enterprise_ids))
                -> select() -> toArray();
            foreach ($enterprises as $vo){
                if(!empty($enterprise_map[$vo["enterprise_id"]])){
                    $indexs = $enterprise_map[$vo["enterprise_id"]];
                    foreach ($indexs as $index){
                        $data[$index]["enterprise_name"] = $vo["enterprise_name"];
                        $data[$index]["contacts"] = $vo["contacts"];
                        $data[$index]["phone"] = $vo["phone"];
                        $data[$index]["fixed_telephone"] = $vo["fixed_telephone"];
                    }
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
            if(isset($data["create_time"])){
                $data["create_time_text"] = "";
                if($data["create_time"] > 0){
                    $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
                }
                unset($data["create_time"]);
            }
            if(isset($data["approval_time"])){
                $data["approval_time_text"] = "";
                if($data["approval_time"] > 0){
                    $data["approval_time_text"] = date("Y-m-d",$data["approval_time"]);
                }
                unset($data["approval_time"]);
            }
            if(!empty($data["department"])){
                switch ($data["department"]){
                    case "PROJECT":
                        $data["department_text"] = "项目部";
                        break;
                    case "DICHOTOMY":
                        $data["department_text"] = "两化部";
                        break;
                    case "KNOWLEDGE":
                        $data["department_text"] = "知识产权部";
                        break;
                    case "FINANCE":
                        $data["department_text"] = "财务部";
                        break;
                    default:
                        $data["department_text"] = "未知部门";
                }
//                unset($data["department"]);
            }
            $data["COMPOSE"] = false;
            $data["PUBLICITY"] = false;
            if(!empty($data["state"])){
                if($data["state"] == "REJECT"){
                    $data["describe"] = "驳回";
                }
                switch ($data["state"]){
                    case "PUBLICITY":
                        $data["PUBLICITY"] = true;
                }
            }
            if(isset($data["settlement"])){
                $data["settlement_text"] = "否";
                if($data["settlement"] == "Y"){
                    $data["settlement_text"] = "是";
                }
                unset($data["settlement"]);
            }

            if(isset($data["declare_time"])){
                $data["declare_time_text"] = "";
                if($data["declare_time"] > 0){
                    $data["declare_time_text"] = date("Y-m-d",$data["declare_time"]);
                }
                unset($data["declare_time"]);
            }

            if(isset($data["publicity_time"])){
                $data["publicity_time_text"] = "";
                if($data["publicity_time"] > 0){
                    $data["publicity_time_text"] = date("Y-m-d",$data["publicity_time"]);
                }
                unset($data["publicity_time"]);
            }

            if(isset($data["collection_time"])){
                $data["collection_time_text"] = "";
                if($data["collection_time"] > 0){
                    $data["collection_time_text"] = date("Y-m-d",$data["collection_time"]);
                }
                unset($data["collection_time"]);
            }

            if(isset($data["signing_time"])){
                $data["signing_time_text"] = "";
                if($data["signing_time"] > 0){
                    $data["signing_time_text"] = date("Y-m-d",$data["signing_time"]);
                }
                unset($data["signing_time"]);
            }

            if(isset($data["invoice_time"])){
                $data["invoice_time_text"] = "";
                if($data["invoice_time"] > 0){
                    $data["invoice_time_text"] = date("Y-m-d",$data["invoice_time"]);
                }
                unset($data["invoice_time"]);
            }
            if(isset($data["img_file_path"])){
                $data["COMPOSE"] = true;
                $data["img_file_path"] = json_decode($data["img_file_path"],true);
                foreach($data["img_file_path"] as &$vo){
                    $vo["preview_name"] = $vo["name"];
                    if(strlen($vo["preview_name"]) > 20){
                        $vo["preview_name"] = mb_substr($vo["preview_name"],0,10)."...";
                    }
                }
            }
        }else{
            if(isset($data["orders_id"])){
                unset($data["orders_id"]);
            }

            if(isset($data["project_id"]) && $data["project_id"] <= 0){
                $this -> error("请选择项目");
            }

            if(isset($data["enterprise_id"]) && $data["enterprise_id"] <= 0){
                $this -> error("请选择企业");
            }

            if(!empty($data["responsible_user_ids"])){
                $data["responsible_user_ids"] = explode(",",$data["responsible_user_ids"]);
                foreach ($data["responsible_user_ids"] as &$responsible_user_id){
                    $responsible_user_id = (int) $responsible_user_id;
                }
                $data["responsible_user_ids"] = json_encode($data["responsible_user_ids"]);
            }

            if(empty($data["department"])){
                $this -> error("请选择部门");
            }

            if(isset($data["receiving_commission"])){
                if($data["receiving_commission"] === null
                    || sizeof(explode(".",$data["receiving_commission"])) > 2
                    || $data["receiving_commission"] < 0){
                    $this -> error("请输入正确的商务人员提成");
                }
                $data["receiving_commission"] = round($data["receiving_commission"],MONEY_ACCURACY);
            }

            if(isset($data["responsible_commission"])){
                if($data["responsible_commission"] === null
                    || sizeof(explode(".",$data["responsible_commission"])) > 2
                    || $data["responsible_commission"] < 0){
                    $this -> error("请输入正确的技术咨询师提成");
                }
                $data["responsible_commission"] = round($data["responsible_commission"],MONEY_ACCURACY);
            }

            if(isset($data["total_commissio"])){
                if($data["total_commissio"] === null
                    || sizeof(explode(".",$data["total_commissio"])) > 2
                    || $data["total_commissio"] < 0){
                    $this -> error("请输入正确的总提成金额");
                }
                $data["total_commissio"] = round($data["total_commissio"],MONEY_ACCURACY);
            }

            if(isset($data["publicity_subsidy"])){
                if($data["publicity_subsidy"] === null
                    || sizeof(explode(".",$data["publicity_subsidy"])) > 2
                    || $data["publicity_subsidy"] < 0){
                    $this -> error("请输入正确的公示补助额度");
                }
                $data["publicity_subsidy"] = round($data["publicity_subsidy"],MONEY_ACCURACY);
            }

            if(isset($data["first_subsidy"])){
                if($data["first_subsidy"] === null
                    || sizeof(explode(".",$data["first_subsidy"])) > 2
                    || $data["first_subsidy"] < 0){
                    $this -> error("请输入正确的首期补助额度");
                }
                $data["first_subsidy"] = round($data["first_subsidy"],MONEY_ACCURACY);
            }

            if(isset($data["first_service_cost"])){
                if($data["first_service_cost"] === null
                    || sizeof(explode(".",$data["first_service_cost"])) > 2
                    || $data["first_service_cost"] < 0){
                    $this -> error("请输入正确的首期服务费");
                }
                $data["first_service_cost"] = round($data["first_service_cost"],MONEY_ACCURACY);
            }

            if(isset($data["second_subsidy"])){
                if($data["second_subsidy"] === null
                    || sizeof(explode(".",$data["second_subsidy"])) > 2
                    || $data["second_subsidy"] < 0){
                    $this -> error("请输入正确的第二期补助额度");
                }
                $data["second_subsidy"] = round($data["second_subsidy"],MONEY_ACCURACY);
            }

            if(isset($data["second_service_cost"])){
                if($data["second_service_cost"] === null
                    || sizeof(explode(".",$data["second_service_cost"])) > 2
                    || $data["second_service_cost"] < 0){
                    $this -> error("请输入正确的第二期服务费");
                }
                $data["second_service_cost"] = round($data["second_service_cost"],MONEY_ACCURACY);
            }

            if(isset($data["third_subsidy"])){
                if($data["third_subsidy"] === null
                    || sizeof(explode(".",$data["third_subsidy"])) > 2
                    || $data["third_subsidy"] < 0){
                    $this -> error("请输入正确的第三期补助额度");
                }
                $data["third_subsidy"] = round($data["third_subsidy"],MONEY_ACCURACY);
            }

            if(isset($data["third_service_cost"])){
                if($data["third_service_cost"] === null
                    || sizeof(explode(".",$data["third_service_cost"])) > 2
                    || $data["third_service_cost"] < 0){
                    $this -> error("请输入正确的第三期服务费");
                }
                $data["third_service_cost"] = round($data["third_service_cost"],MONEY_ACCURACY);
            }

            if(isset($data["contract_subsidy"])){
                if($data["contract_subsidy"] === null
                    || sizeof(explode(".",$data["contract_subsidy"])) > 2
                    || $data["contract_subsidy"] < 0){
                    $this -> error("请输入正确的合同签约额");
                }
                $data["contract_subsidy"] = round($data["contract_subsidy"],MONEY_ACCURACY);
            }

            if(isset($data["invoice_money"])){
                if($data["invoice_money"] === null
                    || sizeof(explode(".",$data["invoice_money"])) > 2
                    || $data["invoice_money"] < 0){
                    $this -> error("请输入正确的开票金额");
                }
                $data["invoice_money"] = round($data["invoice_money"],MONEY_ACCURACY);
            }

            if(!empty($data["declare_time"])){
                $data["declare_time"] = strtotime($data["declare_time"]);
            }

            if(!empty($data["publicity_time"])){
                $data["publicity_time"] = strtotime($data["publicity_time"]);
            }

            if(!empty($data["collection_time"])){
                $data["collection_time"] = strtotime($data["collection_time"]);
            }

            if(!empty($data["signing_time"])){
                $data["signing_time"] = strtotime($data["signing_time"]);
            }

            if(!empty($data["invoice_time"])){
                $data["invoice_time"] = strtotime($data["invoice_time"]);
            }

            if(isset($data["img_file_path"]) && is_array($data["img_file_path"])){
                $data["img_file_path"] = json_encode($data["img_file_path"]);
            }
        }
    }

    private function baseQuery($query,array $params = array()){
//        $user = $this -> app -> session -> get('user');
        $query = $query -> alias("a")
            -> leftjoin($this -> orders_process_table . " b","a.state = b.state")
            -> join($this -> project_table . " c","a.project_id = c.project_id");
//        if($user["id"] != 10000){
//            $query = $query -> where("JSON_CONTAINS(receiving_user_ids -> '$[*]',JSON_ARRAY({$user["id"]}),'$')");
//        }
        if(isset($params["orders_id"]) && strlen($params["orders_id"]) > 0){
            $query = $query -> where("a.orders_id",$params["orders_id"]);
        }
        if(!empty($params["approval_user_id"])){
            $query = $query -> where("a.approval_user_id",$params["approval_user_id"]);
        }
        if(isset($params["department"]) && strlen($params["department"]) > 0 && $params["department"] != "all"){
            $query = $query -> where("a.department",$params["department"]);
        }
        if(isset($params["project_name"]) && strlen($params["project_name"]) > 0){
            $query = $query -> whereLike("c.project_name","%{$params["project_name"]}%");
        }
        if(!empty($params["start_date"])){
            $query = $query -> where("create_date",">=",strtotime($params["start_date"]));
        }
        if(!empty($params["end_date"])){
            $query = $query -> where("create_date","<=",strtotime($params["end_date"]));
        }
        if(isset($params["responsible_username"]) && strlen($params["responsible_username"]) > 0){
            $ids = Db::table($this -> system_user_table)
                -> whereLike("username|nickname","%{$params["responsible_username"]}%")
                -> field(array("id")) -> select() -> toArray();
            $JSON_ARRAY = "";
            foreach ($ids as $id){
                $JSON_ARRAY .= ",'{$id["id"]}'";
            }
            if(strlen($JSON_ARRAY) <= 0){
                $JSON_ARRAY = "0";
            }else{
                $JSON_ARRAY = substr($JSON_ARRAY,1);
            }
            $query = $query -> where("JSON_CONTAINS(responsible_user_ids -> '$[*]',JSON_ARRAY({$JSON_ARRAY}),'$')");
        }
        if(isset($params["enterprise_name"]) && strlen($params["enterprise_name"]) > 0){
            $enterprise_ids = array();
            $enterprise_id = Db::table($this -> enterprise_table)
                -> whereLike("enterprise_name","%{$params["enterprise_name"]}%")
                -> field(array("enterprise_id")) -> select() -> toArray();
            foreach ($enterprise_id as $id){
                array_push($enterprise_ids,$id["enterprise_id"]);
            }
            $query = $query -> whereIn("enterprise_id",$enterprise_ids);
        }
        return $query;
    }

    /**
     * 项目列表
     * 
     */
    public function index(){
        $this -> title = "项目列表";
        $list = Order::select();
        $this->fetch('index', [
            'list'=> $list,
        ]);
    }

    /**
     * 导出Excel表
     * @menu true
     * @auth true
     */
    public function exportExcel(){
        $user = $this -> app -> session -> get('user');
        if(!$user){
            $this->redirect(url('@admin/login'));
        }
        $params = $this -> request -> get();
        $filename = "项目订单";
        $field = array("a.*","c.project_level_id","c.project_name","c.province_region_id","c.city_region_id","c.area_region_id");
        $field_text = array(
//            "商务人员",
            "订单编号","签约时间","联系人","手机号码","固定电话号码","区域","客户名称","项目名称","级别","部门","数量","付款方式","下单时间","申报时间",
//            "责任人",
            "公示时间","公示补助额度","首期补助下达金额","首期服务费收到","二期补助下达金额","二期服务费收到","三期补助下达金额","三期服务费收到","合计补助额","合计收入服务费",
            "合同签约额（含第三方）","备注","收款时间","开票日期","发票号码","开票内容","开票金额");
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
                -> order("a.orders_id","desc")
                -> select() -> toArray();
            $this -> _index_page_filter($data);
            $index = 0;
            foreach ($data as $item) {
                if($index == $limit){ //每次写入1000条数据清除内存
                    $index = 0;
                    ob_flush();//清除内存
                    flush();
                }
                $index++;
                fputcsv($fp,array(
//                    $item["receiving_user_id_text"],
                    $item["orders_id"],
                    $item["signing_time_text"],
                    $item["contacts"],
                    $item["phone"],
                    $item["fixed_telephone"],
                    "{$item["province_region_id_text"]}、{$item["city_region_id_text"]}、{$item["area_region_id_text"]}",
                    $item["enterprise_name"],
                    $item["project_name"],
                    $item["level_name"],
                    $item["department_text"],
                    $item["order_number"],
                    $item["payment_method"],
                    $item["create_time_text"],
                    $item["declare_time_text"],
//                    $item["responsible_user_id_text"],
                    $item["publicity_time_text"],
                    $item["publicity_subsidy"],
                    $item["first_subsidy"],
                    $item["first_service_cost"],
                    $item["second_subsidy"],
                    $item["second_service_cost"],
                    $item["third_subsidy"],
                    $item["third_service_cost"],
                    ($item["first_subsidy"] + $item["second_subsidy"] + $item["third_subsidy"]),
                    ($item["first_service_cost"] + $item["second_service_cost"] + $item["third_service_cost"]),
                    $item["contract_subsidy"],
                    $item["remarks"],
                    $item["collection_time_text"],
                    $item["invoice_time_text"],
                    $item["invoice_number"],
                    $item["invoice_content"],
                    $item["invoice_money"]
                ));
            }
            ob_flush();
            flush();
        }
        ob_end_clean();
        exit();
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this -> _applyFormToken();
        $now_time = time();
        if($this -> request -> isGet()){
            $this -> fetch("form",array("COMPOSE" => false,"PUBLICITY" => false));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            // $this -> handlerData($params,false);
            // $params["img_file_path"] = json_encode(array());
            // $params["publicity_result"] = "";
            // $params["invoice_content"] = "";
            // $params["remarks"] = "";
            // $params["state"] = "ORDER";
            // $params["create_time"] = $now_time;
            // $params["create_date"] = strtotime(date("Y-m-d",$now_time));
            // Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") :  $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 添加
     */
    public function add2() 
    {
        $this->fetch('form2');
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $orders_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($orders_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> alias("a")
                -> join($this -> project_table." c","a.project_id = c.project_id")
                -> join($this -> enterprise_table." d","a.enterprise_id = d.enterprise_id")
                -> where("a.orders_id",$orders_id)
                -> field(array("a.*","c.project_name","d.enterprise_name")) -> find();
            $this -> handlerData($vo);
            $this -> fetch("form",array("vo" => $vo,"COMPOSE" => $vo["COMPOSE"],"PUBLICITY" => $vo["PUBLICITY"]));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table) -> where("orders_id",$orders_id)
                -> update($params) > 0 ? $this -> success("编辑成功") :  $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $orders_id
     * @throws \think\db\exception\DbException
     */
    public function remove($orders_id){
        $this -> _applyFormToken();
        Db::table($this -> table) -> where("orders_id",$orders_id)
            -> delete() > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在或已被他人删除");
    }

  
    /**
     * 审核
     * @param $orders_id
     */
    public function process($orders_id)
    {
        $orderInfo = $this->getOrderInfo($orders_id);
       
        $this->fetch('process', ['order'=>$orderInfo['order'],
            'project_name'=>$orderInfo['project_name'],
            'enterprise_name' => $orderInfo['enterprise_name'],
            'approval_user' => $orderInfo['approval_user'],
            'needReview' => $orderInfo['needReview'],
            'rejectOperator' => $orderInfo['rejectOperator'],
            'processLog' => $orderInfo['processLog'],
        ]);
       
    }
    /**
     * 获取订单信息
     * @param $orders_id  
     */
    private function getOrderInfo($orders_id)
    {
        $order = Order::where('orders_id', $orders_id)->find();
        if (! $order) {
            return;
        }
        // 项目名
        $project_name = Db::table('zp_project')->where('project_id', $order['project_id'])
            ->value('project_name');
        // 企业名
        $enterprise_name = Db::table('zp_enterprise', $order['enterprise_id'])
            ->value('enterprise_name');
        // 审核人
        $approval_user = Db::table('system_user')
            ->where('id', $order['approval_user_id'])->value('username');
        // 是否需要审核
        $needReview = 0;
        if (in_array($order['stage'], $this->needReviewProcesses)) {
            $needReview = 1;
        } 
        // 是否驳回
        $rejectOperator =null;
        $processLog = null;
        if ($order['state'] == "REJECT") {
            $rejectOperatorId = $order->rejectInfo[0]->user_id;
            $rejectOperator = getOperatorUsername($rejectOperatorId);

            // 获取流程记录
            $processLog = $order->getProcessLog($orders_id);
        }  
        return array(
            'order' => $order,
            'project_name'=> $project_name, 
            'enterprise_name' => $enterprise_name, 
            'approval_user' => $approval_user,
            'needReview' => $needReview,
            'rejectOperator' => $rejectOperator,
            'processLog' => $processLog,
        );
    }

  /**
   * @auth true
   * 审核通过 阶段值+1 阶段值是审核阶段数组的索引 代表当前审核的阶段
   * @param $orders_id
   * 
   */
    public function agree($orders_id, $stage) {
      $query = Db::table('zp_orders')->where('orders_id', $orders_id);
      $order = $query->find();
      $operatorId = Session("user")['id'];
      if (! $order) {
        $this->error("该订单不存在");
      } 
      // 阶段+1
      $nextStage = (int)$order['stage']+1;
     
      $query->update(['stage'=> $nextStage]);
      if ($stage == '审核') {
        $query->update(['approval_user_id'=> $operatorId, 'approval_time'=>\time()]);
      }
      // 记录操作
      $res = ApprovalLog::insert([
          'order_id'=>$orders_id, 
          'stage' => $stage,
          'operator' => $operatorId,
          'opt_time' => time(),
        ]);
      if ($res < 0) {
        $this->error('审核失败');
      }
    }
    /**
     * 驳回
     */
    public function reject2(Request $request) 
    {
      $orders_id = (int)$request->param('orders_id');
      $rejectReason = $request->param('rejectReason');
      $stage = $request->param('stage');
      $order = Order::find($orders_id);
      if (! $order) {
          $this->error("该订单不存在");
      }   
      // 修改订单状态
     $update = Order::where('orders_id', $orders_id)->update(['state' => 'REJECT']);
      
      if ($update < 0) {
        $this->error("更新订单失败");
      }
      // 修改驳回日志表
      $user = $this -> app -> session -> get('user'); 
      $insert = Db::table('zp_orders_reject_log')->insert([
        "user_id" => $user["id"],
        "orders_id" => $orders_id,
        "stage" => $stage,
        "reason" => $rejectReason,
        "create_date" => \time(),
        "create_time" => \time(),
      ]);
      if ($insert < 0) {
        $this->error("修改日志表失败");
      }

    }
    /**
     * 返回到指定流程
     */
    public function approval(Request $request)
    {
        $opt_time = $request->param('opt_time');
        $order_id = $request->param('orders_id');
        $order = Order::find($order_id);
        if ($order->stage == '初审') {
            $update = Order::where('orders_id', $order_id)->update(['state'=>'', 'stage'=>0]);
            return;
        }
        return $order->backStage($order_id, $opt_time);
    }
    /**
     * 查看审核记录
     * @param $order_id 订单编号
     */
    public function getRecord($order_id)
    {
        try {
            $order = Order::find($order_id);
            $logInfo = $order->getProcessLog($order_id); 
            $rejectInfo = $order->rejectInfo;
        } catch (Exception $e) {
            $this->error("没有获得相关记录");
        }

        $this->fetch('record', [
            'logInfo' => $logInfo,
            'rejectInfo' => $rejectInfo,
        ]);
         
    }

    /**
     * 完成审核
     * @param $order_id
     */
    public function finish($order_id) 
    {
        // 将订单状态改为完成状态
        Order::where('orders_id', $order_id)->update(['state' => 'FINISH']);
    }

    public function check()
    {
        $type = request()->param('type');
        $name = request()->param('name');
        switch ($type) {
            case 'user':
                $res = User::where('username', $name)->find();
                break;
            case 'project':
                $res = Project::where('project_name', $name)->find();
                break;
            case 'enterprise':
                $res = Enterprise::where('enterprise_name', $name)->find();
                break;
        }
        return is_null($res) ? 0 : 1;
    }

    /**
     * 下单
     * @auth true
     * @menu true
     */
    public function place(Request $request)
    {
        $check = $request->checkToken('__token__', $request->param());
        if(false === $check) {
            throw new ValidateException('invalid token');
        }
        $formData = \request()->param();
        unset($formData['__token__']);
        // 获取项目 企业 用户 id
        $formData['project_id'] = Project::where('project_name', $formData['project_id'])->value('project_id');
        $formData['enterprise_id'] = Enterprise::where('enterprise_name', $formData['enterprise_id'])
            ->value('enterprise_id');
        // 审核人id
        for ($i = 0; $i < count($formData['responsible_user_ids']); $i++) {
            $userId = User::where('username', $formData['responsible_user_ids'][$i])->value('id');  
            $formData['responsible_user_ids'][$i] = $userId;
        }
        $formData['responsible_user_ids'] = json_encode($formData['responsible_user_ids']);

        // 接单人id
        for ($i = 0; $i < count($formData['receiving_user_ids']); $i++) {
            $userId = User::where('username', $formData['receiving_user_ids'][$i])->value('id');
            $formData['receiving_user_ids'][$i] = $userId;
        }
        $formData['receiving_user_ids'] = json_encode($formData['receiving_user_ids']);
        
        // 创建时间
        $formData['create_time'] = time();
        $formData['stage'] = 0;
        
        $validate = new OrderValidate;
        $res =$validate->check($formData);

        if ($res) {
            Order::insert($formData );
            // $this->redirect('/admin.html#/orders/index/index.html');
            $this->redirect('/zp/public/index.php/admin.html#/zp/public/index.php/orders/index/index');
        } else {
            echo $validate->getError();
        }
     
    }

    /**
     * 查看订单技术咨询师
     * @param $orders_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function responsibleInfo($orders_id){
        $orders = Db::table($this -> table)
            -> where("orders_id",$orders_id)
            -> field(array("responsible_user_ids")) -> find();
        $ids = json_decode($orders["responsible_user_ids"],true);
        $this -> _query($this -> system_user_table)
            -> whereIn("id",$ids) -> limit(sizeof($ids))
            -> field(array("username","nickname","headimg"))
            -> page(true, true, false, 0);
    }

    public function test()
    {
        $this->page();
    }
}