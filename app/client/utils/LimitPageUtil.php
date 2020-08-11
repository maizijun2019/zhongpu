<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 16:06
 */

namespace app\client\utils;

use Exception;
use think\facade\Db;

class LimitPageUtil
{
    /**
     * 限制分页
     * @param int $page 当前页数
     * @param int $sum 当前条数
     * @param int $count 总条数
     * @param array $list 数据
     * @return array
     */
    public static function limitPage(int $page,int $sum,int $count,array $list){
        $data = array(
            "page" => $page,
            "sum" => $sum,
            "list" => $list,
            "count" => $count,
            "total_page" => ceil($count / $sum)
        );
        $data["next_page"] = $data["total_page"] > $page ? true : false;
        return $data;
    }

    /**
     * 分页查询
     * @param int $page 当前页数
     * @param int $sum 当前条数
     * @param string $table_name 首表名
     * @param string $alias 首表别名
     * @param array $next_json 关联条件
     * @param array $where 查询条件
     * @param array $wherein in查询条件
     * @param array $field 查询字段
     * @param string $order_field 排序字段
     * @param string $order 排序条件
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function limitQuery(int $page,int $sum,string $table_name,string $alias = "a",array $next_json = array(),
                                      array $where = array(),$wherein = array(),array $field = array(),string $order_field = "",string $order = "DESC"){
        $sql = Db::table($table_name);
        if(strlen($alias) > 0){
            $sql = $sql -> alias($alias);
        }
        if(sizeof($next_json) > 0){
            foreach ($next_json as $key => $next){
                if(!empty($next["join"]) && is_string($next["join"])
                    && !empty($next["table_name"]) && is_string($next["table_name"])
                    && !empty($next["on"]) && is_string($next["on"])){
                    switch (strtoupper($next["join"])){
                        case "JOIN":
                            $sql = $sql -> join($next["table_name"],$next["on"]);
                            break;
                        case "LEFTJOIN":
                            $sql = $sql -> leftJoin($next["table_name"],$next["on"]);
                            break;
                        case "RIGHTJOIN":
                            $sql = $sql -> rightJoin($next["table_name"],$next["on"]);
                            break;
                        case "FULLJOIN":
                            $sql = $sql -> fullJoin($next["table_name"],$next["on"]);
                            break;
                        case "WITHJOIN":
                            $sql = $sql -> withJoin($next["table_name"],$next["on"]);
                            break;
                        default:
                            throw new Exception("SQL关联条件不正确");
                    }
                    continue;
                }
                throw new Exception("SQL关联条件不正确");
            }
        }
        if(sizeof($where) > 0){
            $sql = $sql -> where($where);
        }
        if(sizeof($wherein) == 2){
            $sql = $sql -> whereIn($wherein[0],$wherein[1]);
        }
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        $count = $sql -> count();
        if(strlen($order_field) > 0){
            switch (strtoupper($order)){
                case "ASC":
                case "DESC":
                    $sql = $sql -> order($order_field,$order);
                    break;
                default:
                    throw new Exception("SQL排序条件不正确");
            }
        }
        $list = $sql -> limit(($page - 1) * $sum,$sum) -> select() -> toArray();
        return self::limitPage($page,$sum,$count,$list);
    }
}