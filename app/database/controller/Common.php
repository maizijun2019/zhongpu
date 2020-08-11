<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/5/30
 * Time: 14:00
 */

namespace app\database\controller;

use Exception;
use think\admin\Controller;
use think\App;
use think\facade\Db;

class Common extends Controller
{
    protected $table = "zp_public_database";
    private $type_table = "zp_public_database_type";

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->_applyFormToken();
    }

    public function typeList(){
        return json_encode(Db::table($this -> type_table) -> order("type_id","asc") -> select());
    }

    protected function _index_page_filter(&$data){
        for($i = 0;$i < sizeof($data);$i++){
            $data[$i]["create_time"] = date("Y-m-d H:i:s",$data[$i]["create_time"]);
        }
    }

    public function index(){
        $this -> title = "公共文件列表";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table) -> alias("a")
            -> leftJoin($this -> type_table . " b","a.type_id = b.type_id")
            -> leftJoin("system_user c","a.user_id = c.id")
            -> where("a.is_delete",0);
        if(!empty($params["type_id"]) && $params["type_id"] != "all"){
            $query -> where("b.type_id",$params["type_id"]);
        }
        if(!empty($params["date"])){
            $date = explode(" - ",$params["date"]);
            if(sizeof($date) >= 2){
                $date[0] = strtotime($date[0]);
                $date[1] = strtotime($date[1]);
                $query -> whereBetween("a.create_time",$date);
            }
        }
        $query -> field(array("a.public_database_id","c.username","a.file_name","a.remarks","a.create_time","IFNULL(b.type,'未分类') type_text"))
            -> order("a.public_database_id","desc")
            -> page(true, true, false, 0);
    }

    public function add(){
        if($this -> request -> isGet()){
            $this -> fetch("upload");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if($_FILES){
                echo 1;
            }
            echo json_encode($params);
        }
    }

    public function edit($public_database_id){
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("public_database_id",$public_database_id) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(isset($params["public_database_id"])){
                unset($params["public_database_id"]);
            }
            Db::table($this -> table) -> where("public_database_id",$public_database_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("编辑失败");
        }
    }

    public function remove($public_database_id){
        Db::table($this -> table)
            -> where("public_database_id",$public_database_id)
            -> update(array("is_delete" => 1)) > 0 ? $this -> success("删除成功") : $this -> error("该数据不存在,可能已被他人删除");
    }

    public function type(){
        $this -> title = "分类管理";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> type_table) -> alias("a");
        if(isset($params["type"]) && strlen($params["type"]) > 0){
            $query -> whereLike("type","%{$params["type"]}%");
        }
        $query -> field(array("a.type_id","a.type"))
            -> order("a.type_id","asc")
            -> page(true, true, false, 0);
    }

    public function addType(){
        if($this -> request -> isGet()){
            $this -> fetch("type_form");
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $flag = 0;
            try{
                $flag = Db::table($this -> type_table) -> insert($params);
            }catch (Exception $exception){
                $errorMessage = $exception -> getMessage();
                if(strlen($errorMessage) > 0 && $flag <= 0 &&
                    strpos("SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$params["type"]}' for key 'type'",$errorMessage) !== false){
                    $this -> error("该分类名称已存在");
                }
            }
            if($flag > 0){
                $this -> success("添加成功");
            }
            $this -> error("添加失败");
        }
    }

    public function editType($type_id){
        if($this -> request -> isGet()){
            $vo = Db::table($this -> type_table) -> where("type_id",$type_id) -> find();
            $this -> fetch("type_form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(isset($params["type_id"])){
                unset($params["type_id"]);
            }
            $flag = 0;
            try{
                $flag = Db::table($this -> type_table) -> where("type_id",$type_id) -> update($params);
            }catch (Exception $exception){
                $errorMessage = $exception -> getMessage();
                if(strlen($errorMessage) > 0 && $flag <= 0 &&
                    strpos("SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$params["type"]}' for key 'type'",$errorMessage) !== false){
                    $this -> error("该分类名称已存在");
                }
            }
            if($flag > 0){
                $this -> success("编辑成功");
            }
            $this -> error("编辑失败");
        }
    }

    public function removeType($type_id){
        $this->_delete($this -> type_table);
    }
}