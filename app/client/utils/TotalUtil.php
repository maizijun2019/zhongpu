<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/9
 * Time: 13:55
 */

namespace app\client\utils;

class TotalUtil
{
    /**
     * 将返回的数据集转换成树
     * @param  array   $list  数据集
     * @param  string  $pk    主键
     * @param  string  $pid   父节点名称
     * @param  string  $child 子节点名称
     * @param  integer $root  根节点ID
     * @return array          转换后的树
     */
    public static function list_to_tree($list,$pk = 'id',$pid = 'pid',$child = 'sub',$root = 0) {
        $tree = array();// 创建Tree
        $trees = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
                $refer[$data[$pk]][$child] = array();
            }

            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[$data[$pk]] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        foreach ($tree as $vo){
            if(!isset($vo[$child])){
                $vo[$child] = array();
            }
            array_push($trees,$vo);
        }
        return $trees;
    }
}