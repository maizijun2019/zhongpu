<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/12
 * Time: 10:40
 */

namespace app\consumer\controller;

use app\client\model\ConsumerModel;
use think\admin\Controller;
use think\facade\Db;

/**
 * 前端用户管理
 * Class Index
 * @package app\consumer\controller
 */
class Index extends Controller
{
    protected $table = "zp_consumer";
    private $enterprise_table = "zp_enterprise";

    protected function _index_page_filter(&$data){
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    private function handlerData(array &$data,bool $display = true){
        if($display){
            if(!empty($data["create_time"])){
                $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
                unset($data["create_time"]);
            }
        }else{
            if(isset($data["username"])){
                if(!ConsumerModel::checkUsername($data["username"])){
                    $this -> error(ConsumerModel::$NAME_STANDARD);
                }
            }
            if(isset($data["password"]) && isset($data["check_password"])){
                if($data["password"] !== $data["check_password"]){
                    $this -> error("两次密码不一致");
                }
                unset($data["check_password"]);
                if(!ConsumerModel::checkPassword($data["password"])){
                    $this -> error(ConsumerModel::$PASSWORD_STANDARD);
                }
                $data["password"] = ConsumerModel::encodePassword($data["password"]);
            }
            if(isset($data["phone"])){
                if(!ConsumerModel::checkPhone($data["phone"])){
                    $this -> error(ConsumerModel::$PHONE_STANDARD);
                }
            }
        }
    }

    /**
     * 用户列表
     * @menu true
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $this -> title = "用户列表";
        $params = $this -> request -> get();
        $query = $this -> _query($this -> table) -> alias("a")
            -> leftJoin($this -> enterprise_table." b","a.consumer_id = b.consumer_id");
        if(isset($params["username"]) && strlen($params["username"]) > 0){
            $query -> whereLike("username","%{$params["username"]}%");
        }
        if(isset($params["phone"]) && strlen($params["phone"]) > 0){
            $query -> whereLike("phone","%{$params["phone"]}%");
        }
        $query -> field(array("a.consumer_id","a.username","a.nickname","a.phone","a.create_time","IFNULL(b.enterprise_name,'') enterprise_name"))
            -> order("a.consumer_id","desc")
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
            $params["create_time"] = time();
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 修改
     * @menu true
     * @auth true
     * @param $consumer_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($consumer_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("consumer_id",$consumer_id)
                -> field(array("consumer_id","username","phone")) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(isset($params["password"]) && strlen($params["password"]) <= 0){
                unset($params["password"]);
                unset($params["check_password"]);
            }
            $this -> handlerData($params,false);
            if(isset($params["consumer_id"])){
                unset($params["consumer_id"]);
            }
            Db::table($this -> table) -> where("consumer_id",$consumer_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $consumer_id
     * @throws \think\db\exception\DbException
     */
    public function remove($consumer_id){
        Db::table($this -> table)
            -> where("consumer_id",$consumer_id)
            -> delete() > 0 ? $this -> success("删除成功") :  $this -> error("该数据不存在或已被他人删除");
    }
}