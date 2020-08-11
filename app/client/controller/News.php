<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/1
 * Time: 15:01
 */

namespace app\client\controller;

use app\client\model\NewsModel;
use app\client\model\NewsTypeModel;
use app\client\utils\JSONUtil;
use app\client\utils\LimitPageUtil;
use app\client\utils\TotalUtil;
use think\admin\Controller;
use think\facade\Db;

class News extends Controller
{
    /**
     * 分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function typeList(){
        $list = Db::table(NewsTypeModel::$TABLE_NAME)
            -> field(array("news_type_id","pid","type_name"))
            -> order("news_type_id","ASC")
            -> select() -> toArray();
        JSONUtil::sendSuccess(TotalUtil::list_to_tree($list,"news_type_id"));
    }

    /**
     * 新闻列表
     * @param int $page
     * @param int $sum
     * @param int $news_type_id 新闻分类id
     * @param string $keyword = 关键词
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function newsList(int $page = 1,int $sum = 10,int $news_type_id = 0,string $keyword = ""){
        $where = array();
        if(strlen($keyword) > 0){
            array_push($where,array("a.title","LIKE","%{$keyword}%"));
        }
        $wherein = array();
        if($news_type_id > 0){
            $wherein = array("news_type_id",NewsTypeModel::subTypeId($news_type_id));
        }
        $data = LimitPageUtil::limitQuery($page,$sum,NewsModel::$TABLE_NAME,"a",
            array(array("join" => "leftjoin","table_name" => NewsTypeModel::$TABLE_NAME." b","on" => "a.news_type_id = b.news_type_id")),
            $where,$wherein,array("a.news_id","a.title","a.create_time","IFNULL(b.type_name,'') type_name"),"a.news_id");
        foreach ($data["list"] as &$vo){
            if(isset($vo["create_time"])){
                $vo["create_time_text"] = date("Y-m-d",$vo["create_time"]);
                unset($vo["create_time"]);
            }
        }
        JSONUtil::sendSuccess($data);
    }

    /**
     * 新闻详情
     * @param int $news_id 新闻id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info(int $news_id){
        $data = array();
        if($news_id > 0){
            $data = Db::table(NewsModel::$TABLE_NAME) -> alias("a")
                -> leftJoin(NewsTypeModel::$TABLE_NAME." b","a.news_type_id = b.news_type_id")
                -> where("a.news_id",$news_id)
                -> field(array("a.news_id","a.title","a.content","a.create_time","IFNULL(b.type_name,'') type_name"))
                -> find();
        }
        if($data === null){
            $data = array();
        }else{
            $data["create_time_text"] = date("Y-m-d",$data["create_time"]);
            unset($data["create_time"]);
        }
        JSONUtil::sendSuccess($data);
    }

    /**
     * 新闻内容
     * @param int $news_id 新闻id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function content(int $news_id){
        echo Db::table(NewsModel::$TABLE_NAME) -> where("news_id",$news_id) -> field(array("content")) -> find()["content"];die;
    }
}