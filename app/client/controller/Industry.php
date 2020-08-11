<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 14:27
 */

namespace app\client\controller;

use app\client\model\IndustryModel;
use app\client\utils\JSONUtil;
use think\admin\Controller;

class Industry extends Controller
{
    /**
     * 获取行业列表
     * @param int $industry_id 行业id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listArray(int $industry_id = 0){
        return JSONUtil::sendSuccess(IndustryModel::queryList($industry_id));
    }
}