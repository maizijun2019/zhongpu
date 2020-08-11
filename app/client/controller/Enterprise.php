<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 13:12
 */

namespace app\client\controller;

use app\client\exception\EnterpriseException;
use app\client\model\ConsumerModel;
use app\client\model\EnterpriseModel;
use app\client\model\ProjectAttributeModel;
use app\client\utils\JSONUtil;
use app\client\utils\LimitPageUtil;
use Exception;
use think\admin\Controller;
use think\facade\Db;

class Enterprise extends Controller
{
    /**
     * 列表数据
     * @param int $page 页数
     * @param int $sum 条数
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listArray(int $page = DEFAULT_PAGE,int $sum = DEFAULT_SUM){
        $consumer = ConsumerModel::checkLogin(true);
        $list = LimitPageUtil::limitQuery($page,$sum,
            EnterpriseModel::$TABLE_NAME,"a",array(),array("consumer_id" => $consumer["consumer_id"]),array(),
            array("enterprise_id","enterprise_name","logo_path","fixed_telephone","industry_id","province_region_id","city_region_id","area_region_id","address","attribute"),
            "a.enterprise_id");
        foreach ($list["list"] as &$vo){
            $vo["attributes_value"] = false;
            if(strlen($vo["attribute"]) > 2){
                $vo["attributes_value"] = true;
            }
            unset($vo["attribute"]);
        }
        JSONUtil::sendSuccess($list);
    }

    /**
     * 企业信息详情
     * @param int $enterprise_id 企业id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info(int $enterprise_id){
        $consumer = ConsumerModel::checkLogin(true);
        $enterprise = Db::table(EnterpriseModel::$TABLE_NAME)
            -> where(array("enterprise_id" => $enterprise_id,"consumer_id" => $consumer["consumer_id"]))
            -> field(array("enterprise_id","enterprise_name","logo_path","fixed_telephone","industry_id",
                "province_region_id","city_region_id","area_region_id","address","attribute"))
            -> find();
        if($enterprise){
            $enterprise["attributes_value"] = false;
            if(strlen($enterprise["attribute"]) > 2){
                $enterprise["attributes_value"] = true;
            }
            unset($enterprise["attribute"]);
            JSONUtil::sendSuccess($enterprise);
        }
        JSONUtil::sendFail();
    }

    /**
     * 添加企业
     * @param string $enterprise_name 企业名称
     * @param string $logo_path logo路径
     * @param string $fixed_telephone 固定电话
     * @param int $industry_id 行业id
     * @param int $province_region_id 省id
     * @param int $city_region_id 市id
     * @param int $area_region_id 区id
     * @param string $address 详细地址
     */
    public function add(string $enterprise_name,string $logo_path,string $fixed_telephone,
                        int $industry_id,int $province_region_id,int $city_region_id,int $area_region_id,string $address){
        if($this -> request -> post()){
            try{
                $consumer = ConsumerModel::checkLogin(true);
                EnterpriseModel::add($consumer["consumer_id"],$enterprise_name,$logo_path,$fixed_telephone,$industry_id,$province_region_id,$city_region_id,$area_region_id,$address);
                JSONUtil::sendSuccess();
            }catch (EnterpriseException $enterpriseException){
                JSONUtil::sendFail($enterpriseException -> getErrorMessage());
            }catch (Exception $exception){
            }
        }
        JSONUtil::sendFail();
    }

    /**
     * 编辑企业
     * @param int $enterprise_id 企业id
     * @param string $enterprise_name 企业名称
     * @param string $logo_path logo路径
     * @param string $fixed_telephone 固定电话
     * @param int $industry_id 行业id
     * @param int $province_region_id 省id
     * @param int $city_region_id 市id
     * @param int $area_region_id 区id
     * @param string $address 详细地址
     */
    public function edit(int $enterprise_id,string $enterprise_name,string $logo_path,string $fixed_telephone,
                         int $industry_id,int $province_region_id,int $city_region_id,int $area_region_id,string $address){
        if($this -> request -> post()){
            try{
                $consumer = ConsumerModel::checkLogin(true);
                if(EnterpriseModel::edit($consumer["consumer_id"],$enterprise_id,$enterprise_name,$logo_path,$fixed_telephone,$industry_id,$province_region_id,$city_region_id,$area_region_id,$address)){
                    JSONUtil::sendSuccess();
                }
                JSONUtil::sendFail("没有任何修改");
            }catch (EnterpriseException $enterpriseException){
                JSONUtil::sendFail($enterpriseException -> getErrorMessage());
            }catch (Exception $exception){
            }
        }
        JSONUtil::sendFail();
    }

    /**
     * 获取属性值
     * @param int $enterprise_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function attribute(int $enterprise_id){
        $consumer = ConsumerModel::checkLogin(true);
        $attribute = Db::table(EnterpriseModel::$TABLE_NAME)
            -> where(array("consumer_id" => $consumer["consumer_id"],"enterprise_id" => $enterprise_id))
            -> field(array("attribute")) -> find();
        if($attribute){
            JSONUtil::sendSuccess(ProjectAttributeModel::getAttributeArray(json_decode($attribute["attribute"],true)));
        }
        JSONUtil::sendFail();
    }

    /**
     * 编辑属性值
     * @param int $enterprise_id 企业id
     * @param array $attribute 属性值
     * @throws \think\db\exception\DbException
     */
    public function editAttribute(int $enterprise_id,array $attribute){
        if($this -> request -> isPost()){
            $consumer = ConsumerModel::checkLogin(true);
            EnterpriseModel::editAttribute($consumer["consumer_id"],$enterprise_id,$attribute);
            JSONUtil::sendSuccess();
        }
        JSONUtil::sendFail();
    }
}