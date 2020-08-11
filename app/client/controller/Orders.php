<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/7/2
 * Time: 09:27
 */

namespace app\client\controller;

use app\client\model\ConsumerModel;
use think\admin\Controller;

class Orders extends Controller
{
    public function order(int $project_id){
        $consumer = ConsumerModel::checkLogin(true);
    }
}