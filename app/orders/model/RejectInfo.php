<?php
namespace app\orders\model;
use think\Model;
use think\facade\Db;

class RejectInfo extends Model
{
  protected $table = "zp_orders_reject_log";
  protected $autoTimestamp = true;


}