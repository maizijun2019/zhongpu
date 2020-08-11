<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 14:25
 */

namespace app\client\model;

use app\client\exception\EnterpriseException;
use Exception;
use think\facade\Db;

class EnterpriseModel
{
    public static $TABLE_NAME = "zp_enterprise";
    public static $FIXED_TELEPHONE_STANDARD = "请填写正确的固定电话号码";

    /**
     * 检测固定电话
     * @param string $fixed_telephone
     * @return bool true：通过，false：不通过
     */
    public static function checkFixedTelephone(string $fixed_telephone){
        if(preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$fixed_telephone)){
            return true;
        }
        return false;
    }

    /**
     * 添加企业
     * @param int $consumer_id 用户id
     * @param string $enterprise_name 企业名称
     * @param string $logo_path logo路径
     * @param string $fixed_telephone 固定电话
     * @param int $industry_id 行业id
     * @param int $province_region_id 省id
     * @param int $city_region_id 市id
     * @param int $area_region_id 区id
     * @param string $address 详细地址
     * @throws EnterpriseException
     */
    public static function add(int $consumer_id,string $enterprise_name,string $logo_path,string $fixed_telephone,
                               int $industry_id,int $province_region_id,int $city_region_id,int $area_region_id,string $address){
        if(empty($enterprise_name)){
            throw new EnterpriseException("请填写企业名称");
        }
        if(!EnterpriseModel::checkFixedTelephone($fixed_telephone)){
            throw new EnterpriseException(self::$FIXED_TELEPHONE_STANDARD);
        }
        if(!IndustryModel::existence($industry_id) || IndustryModel::lastLevel($industry_id)){
            throw new EnterpriseException("请选择最下级行业");
        }
        if(!RegionModel::existence(RegionModel::$PROVINCE_LEVEL,$province_region_id)){
            throw new EnterpriseException("请选择省");
        }
        if(!RegionModel::existence(RegionModel::$CITY_LEVEL,$city_region_id)){
            throw new EnterpriseException("请选择市");
        }
        if(!RegionModel::existence(RegionModel::$AREA_LEVEL,$area_region_id)){
            throw new EnterpriseException("请选择区");
        }
        if(empty($address)){
            throw new EnterpriseException("请填写详细地址");
        }
        if(empty($logo_path)){
            $logo_path = "";
        }
        $insert_flag = 0;
        try{
            $insert_flag = Db::table(self::$TABLE_NAME) -> insert(array(
                "consumer_id" => $consumer_id,
                "enterprise_name" => $enterprise_name,
                "logo_path" => $logo_path,
                "fixed_telephone" => $fixed_telephone,
                "industry_id" => $industry_id,
                "province_region_id" => $province_region_id,
                "city_region_id" => $city_region_id,
                "area_region_id" => $area_region_id,
                "address" => $address,
                "attribute" => "{}"
            ));
//            $insert_flag = Db::execute("INSERT INTO `".self::$TABLE_NAME."`
//            (`consumer_id`,`enterprise_name`,`logo_path`,`fixed_telephone`,`industry_id`,`province_region_id`,`city_region_id`,`area_region_id`,`address`,`attribute`)
//            SELECT {$consumer_id},'{$enterprise_name}','{$logo_path}','{$fixed_telephone}',{$industry_id},{$province_region_id},{$city_region_id},{$area_region_id},'{$address}','{}'
//            FROM DUAL WHERE NOT EXISTS
//            (SELECT `enterprise_id` FROM `".self::$TABLE_NAME."` WHERE `consumer_id` = {consumer_id} LIMIT 1);");
        }catch (Exception $exception){
            if(strpos($exception -> getMessage(),"SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$consumer_id}' for key 'consumer_id'") !== false){
                throw new EnterpriseException("您已经添加过企业");
            }
        }
        if($insert_flag > 0){
            return true;
        }
        return false;
    }

    /**
     * 编辑企业
     * @param int $consumer_id 用户id
     * @param int $enterprise_id 企业id
     * @param string $enterprise_name 企业名称
     * @param string $logo_path logo路径
     * @param string $fixed_telephone 固定电话
     * @param int $industry_id 行业id
     * @param int $province_region_id 省id
     * @param int $city_region_id 市id
     * @param int $area_region_id 区id
     * @param string $address 详细地址
     * @return bool true：编辑成功，false：编辑失败
     * @throws EnterpriseException
     * @throws \think\db\exception\DbException
     */
    public static function edit(int $consumer_id,int $enterprise_id,string $enterprise_name,string $logo_path,string $fixed_telephone,
                                int $industry_id,int $province_region_id,int $city_region_id,int $area_region_id,string $address){
        if(empty($enterprise_name)){
            throw new EnterpriseException("请填写企业名称");
        }
        if(!EnterpriseModel::checkFixedTelephone($fixed_telephone)){
            throw new EnterpriseException(self::$FIXED_TELEPHONE_STANDARD);
        }
        if(!IndustryModel::existence($industry_id) || IndustryModel::lastLevel($industry_id)){
            throw new EnterpriseException("请选择最下级行业");
        }
        if(!RegionModel::existence(RegionModel::$PROVINCE_LEVEL,$province_region_id)){
            throw new EnterpriseException("请选择省");
        }
        if(!RegionModel::existence(RegionModel::$CITY_LEVEL,$city_region_id)){
            throw new EnterpriseException("请选择市");
        }
        if(!RegionModel::existence(RegionModel::$AREA_LEVEL,$area_region_id)){
            throw new EnterpriseException("请选择区");
        }
        if(empty($address)){
            throw new EnterpriseException("请填写详细地址");
        }
        if(empty($logo_path)){
            $logo_path = "";
        }
        if($enterprise_id > 0){
            $update_flag = Db::table(self::$TABLE_NAME)
                -> where(array("enterprise_id" => $enterprise_id,"consumer_id" => $consumer_id))
                -> update(array(
                    "enterprise_name" => $enterprise_name,
                    "logo_path" => $logo_path,
                    "fixed_telephone" => $fixed_telephone,
                    "industry_id" => $industry_id,
                    "province_region_id" => $province_region_id,
                    "city_region_id" => $city_region_id,
                    "area_region_id" => $area_region_id,
                    "address" => $address
                ));
            if($update_flag > 0){
                return true;
            }
        }
        return false;
    }

    /**
     * 编辑属性
     * @param int $consumer_id 用户id
     * @param int $enterprise_id 企业id
     * @param array $attribute 属性值
     * @return bool
     * @throws \think\db\exception\DbException
     */
    public static function editAttribute(int $consumer_id,int $enterprise_id,array $attribute){
        if($enterprise_id > 0 && ProjectAttributeModel::checkAttribute($attribute)){
            $attribute = json_encode($attribute);
            $update_flag = Db::table(self::$TABLE_NAME)
                -> where(array("enterprise_id" => $enterprise_id,"consumer_id" => $consumer_id))
                -> update(array("attribute" => $attribute));
            if($update_flag > 0){
                return true;
            }
        }
        return false;
    }
}