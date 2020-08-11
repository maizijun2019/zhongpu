<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/23
 * Time: 09:33
 */

namespace app\api\model;

use think\Model;

class AreaModel extends Model
{
    protected $table = "area";

    public function getAreaList(array $condition){
        return $this -> where($condition) -> select() -> toArray();
    }

    public function getAreaInfo(array $condition){
        return $this -> where($condition) -> find();
    }
}