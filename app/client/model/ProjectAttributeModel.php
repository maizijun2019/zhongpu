<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/4
 * Time: 13:44
 */

namespace app\client\model;

use think\facade\Db;

class ProjectAttributeModel
{
    public static $TABLE_NAME = "zp_project_attribute";
    public static $ATTRIBUTE_PREFIX_NAME = "project_attribute_id_";

    /**
     * 从属性值中获取所有属性id
     * @param array $attribute
     * @return array
     */
    public static function getAttributeIds(array $attribute){
        $attribute_ids = array();
        $keys = array_keys($attribute);
        foreach ($keys as $key){
            array_push($attribute_ids,str_replace(self::$ATTRIBUTE_PREFIX_NAME,"",$key));
        }
        return $attribute_ids;
    }

    /**
     * @param array $attribute
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getAttributeArray(array $attribute){
        $attribute_ids = self::getAttributeIds($attribute);
        $data = array();
        if(sizeof($attribute_ids) > 0){
            $data = Db::table(self::$TABLE_NAME)
                -> where("display","Y")
                -> whereIn("project_attribute_id",$attribute_ids)
                -> field(array("project_attribute_id","attribute_name","company","required","remarks"))
                -> limit(sizeof($attribute_ids)) -> select() -> toArray();
            foreach ($data as &$vo){
                $vo["value"] = $attribute[self::$ATTRIBUTE_PREFIX_NAME.$vo["project_attribute_id"]];
            }
        }
        return $data;
    }

    /**
     * 检测属性值
     * @param array $attribute 属性值
     * @return bool
     */
    public static function checkAttribute(array &$attribute){
        if(sizeof($attribute) > 0){
            $keys = array_keys($attribute);
            foreach ($keys as $key){
                if(preg_match("/^\d{1,}$/",$key)){
                    if(is_numeric($attribute[$key])){
                        $attribute[ProjectAttributeModel::$ATTRIBUTE_PREFIX_NAME.$key] = (double) $attribute[$key];
                        unset($attribute[$key]);
                        continue;
                    }
                }
                return false;
            }
            return true;
        }
        return false;
    }
}