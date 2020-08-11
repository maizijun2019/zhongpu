<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/19
 * Time: 11:04
 */

namespace app\news\controller;

use app\client\utils\TotalUtil;
use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

class Type extends Controller
{
    protected $table = "zp_news_type";

    public function typeArray(array $where = array()){
        return json_encode($this -> typeTree($where));
    }

    private function typeTree(array $where = array()){
        $sql = Db::table($this -> table);
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
        if(isset($params["news_type_id"]) && $params["news_type_id"] != "all"){
            $query -> whereIn("news_type_id",$this -> subLevel($params["news_type_id"]));
        }
        return $query;
    }

    protected function _index_page_filter(&$data){
        $data = DataExtend::arr2table($data,"news_type_id");
    }

    public function index(){
        $data = Db::table($this -> table) -> order("news_type_id","ASC") -> select() -> toArray();
        $data = TotalUtil::list_to_tree($data,"news_type_id");
        $this -> fetch("",array("list" => $data));
    }

    public function index1(){
        $this -> title = "新闻分类";
        $this -> baseQuery($this -> _query($this -> table),$this -> request -> get())
            -> order("news_type_id","asc") -> page(false, true,false);
    }

    /**
     * 添加
     * @menu true
     * @auth true
     */
    public function add(){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $pid = $this -> request -> get("pid");
            if(empty($pid) || $pid < 0){
                $pid = 0;
            }
            $this -> fetch("form",array(
                "tree" => $this -> typeTree(),
                "pid" => $pid
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            Db::table($this -> table) -> insert($params) > 0 ? $this -> success("添加成功") : $this -> error("添加失败,请刷新页面重试");
        }
    }

    /**
     * 编辑
     * @menu true
     * @auth true
     * @param $news_type_id
     * @throws \think\db\exception\DbException
     */
    public function edit($news_type_id){
        $this->_applyFormToken();
        if($this -> request -> isGet()){
            $tree = $this -> typeTree();
            $vo = array();
            foreach ($tree as $item){
                if($item["news_type_id"] == $news_type_id){
                    $vo = $item;
                    break;
                }
            }
            $this -> fetch("form",array(
                "tree" => $tree,
                "vo" => $vo,
                "pid" => $vo["pid"]
            ));
        }
        if($this -> request -> isPost()){
            $params = $this -> request -> post();
            if(!empty($params["news_type_id"])){
                unset($params["news_type_id"]);
            }
            Db::table($this -> table) -> where("news_type_id",$news_type_id) -> update($params) > 0 ? $this -> success("编辑成功") : $this -> error("没有任何修改");
        }
    }

    /**
     * 删除
     * @menu true
     * @auth true
     * @param $news_type_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function remove($news_type_id){
        $map = array($news_type_id => 1);
        $tree = DataExtend::arr2table(Db::table($this -> table) -> where("news_type_id",">",$news_type_id) -> field(array("news_type_id","pid")) -> order("news_type_id","asc") -> select() -> toArray(),"news_type_id");
        foreach ($tree as $vo){
            if(!empty($map[$vo["pid"]])){
                array_push($map,array($vo["news_type_id"] => 1));
            }
        }
        $ids = array_keys($map);
        $flag = Db::table($this -> table)
            -> whereIn("news_type_id",$ids)
            -> delete();
        if($flag > 0){
            Db::table($this -> attributeTable)
                -> whereIn("news_type_id",$ids)
                -> delete();
            $this -> success("删除成功");
        }
        $this -> error("删除失败,该数据不存在");
    }
}