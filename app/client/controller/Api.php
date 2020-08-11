<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 14:18
 */

namespace app\client\controller;

use app\client\model\ConsumerModel;
use app\client\utils\JSONUtil;
use app\client\utils\UploadUtil;
use think\admin\Controller;

class Api extends Controller
{
    /**
     * 上传企业logo图片
     */
    public function uploadLogoImg(){
        $now_time = time();
        $consumer = ConsumerModel::checkLogin(true);
        $path = UploadUtil::uploadFile("upload/enterprise/{$consumer["consumer_id"]}/".date("Y-m-d",$now_time)."/{$now_time}/","logo_img");
        if($path !== false){
            JSONUtil::sendSuccess(array("path" => $path));
        }
        JSONUtil::sendFail();
    }
}