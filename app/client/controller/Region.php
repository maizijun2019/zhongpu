<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 14:57
 */

namespace app\client\controller;

use app\client\model\RegionModel;
use app\client\utils\JSONUtil;
use think\admin\Controller;

class Region extends Controller
{
    /**
     * 查询地区
     * @param int $level 地区等级，1：省级，2：市级，3：县级
     * @param int $pid 上级地区id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function region(int $level,int $pid = 0){
        JSONUtil::sendSuccess(RegionModel::queryList($level,$pid));
    }

    /**
     * 获取省
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function province(){
        JSONUtil::sendSuccess(RegionModel::queryList(RegionModel::$PROVINCE_LEVEL));
    }

    /**
     * 获取市
     * @param int $province_region_id 省id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function city(int $province_region_id){
        JSONUtil::sendSuccess(RegionModel::queryList(RegionModel::$CITY_LEVEL,$province_region_id));
    }

    /**
     * 获取区
     * @param int $city_region_id 市id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function area(int $city_region_id){
        JSONUtil::sendSuccess(RegionModel::queryList(RegionModel::$AREA_LEVEL,$city_region_id));
    }
}