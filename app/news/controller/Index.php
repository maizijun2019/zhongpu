<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/19
 * Time: 11:12
 */

namespace app\news\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 新闻列表管理
 * Class Index
 * @package app\news\controller
 */
class Index extends Controller
{
    protected $table = "zp_news";
    private $news_type_table = "zp_news_type";

    private function typeTree(array $where = array()){
        $sql = Db::table($this -> news_type_table);
        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }
        return DataExtend::arr2table($sql -> order("news_type_id","asc") -> select() -> toArray(),"news_type_id");
    }

    private function subLevel(int $pid){
        $level_ids[$pid] = 1;
        $tree = $this -> typeTree(array(array("news_type_id",">",$pid)));
        foreach ($tree as $vo){
            if(!empty($level_ids[$vo["pid"]])){
                $level_ids[$vo["news_type_id"]] = 1;
            }
        }
        return array_keys($level_ids);
    }

    private function baseQuery($query,array $params = array()){
        $query = $query -> alias("a")
            -> join($this -> news_type_table." b","a.news_type_id = b.news_type_id");
        if(isset($params["news_type_id"]) && $params["news_type_id"] != "all"){
            $query -> whereIn("a.news_type_id",$this -> subLevel($params["news_type_id"]));
        }
        if(!empty($params["start_date"])){
            $query -> where("create_date",">=",strtotime($params["start_date"]));
        }
        if(!empty($params["end_date"])){
            $query -> where("create_date","<=",strtotime($params["end_date"]));
        }
        return $query;
    }

    private function handlerData(array &$data,$display = true){
        if($display){
            if(!empty($data["create_time"])){
                $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
            }
        }else{
            if(isset($data["news_type_id"])){
                $count = Db::table($this -> news_type_table) -> where("pid",$data["news_type_id"]) -> count();
                if($count > 0){
                    $this -> error("只允许最下层分类添加新闻");
                }
            }
            if(isset($data["news_id"])){
                unset($data["news_id"]);
            }
        }
    }

    protected function _index_page_filter(&$data){
        foreach ($data as &$vo){
            $this -> handlerData($vo);
        }
    }

    /**
     * 新闻列表
     * @menu true
     * @auth true
     */
    public function index(){
        $this -> title = "新闻列表";
        $this -> baseQuery($this -> _query($this -> table),$this -> request -> get())
            -> field(array("a.news_id","a.title","a.create_time","b.type_name"))
            -> order("a.news_id","desc")
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
            $params["create_time"] = time();
            $params["create_date"] = strtotime(date("Y-m-d",$params["create_time"]));
            $this -> handlerData($params,false);
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $news_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($news_id){
        $this -> _applyFormToken();
        if($this -> request -> isGet()){
            $vo = Db::table($this -> table) -> where("news_id",$news_id) -> find();
            $this -> fetch("form",array("vo" => $vo));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            $this -> handlerData($params,false);
            Db::table($this -> table) -> where("news_id",$news_id)
                -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 网页预览
     * @param $news_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info($news_id){
        echo Db::table($this -> table) -> where("news_id",$news_id) -> field(array("content")) -> find()["content"];
    }
}