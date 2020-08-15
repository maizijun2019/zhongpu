<?php
namespace app\orders\model;
use think\Model;
use think\facade\Db;

class User extends Model
{
  protected $table = "system_user";

  // 判断有没下单权限
  public function hasPlaceAuth()
  {
    
  }

  // 判断有没主管权限
  public function hasManagerAuth()
  {

  }




}