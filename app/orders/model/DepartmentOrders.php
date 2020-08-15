<?php
namespace app\orders\model;
use think\Model;
use think\facade\Db;

class DepartmentOrders extends Model
{
  protected $table = "zp_department";

  // 获取部门人员
  public function getMemberIds($department)
  {
    return $this->where('name', $department)->value('user_ids');
  }

  // 获取部门流程
  public static function getProcess($order_id)
  { 
    $order = Order::find($order_id);
    $department = $order->getData('department');
    $res = Db::table('zp_orders_process')->where('department', $department)->field('state')->select();
    $process = json_decode($res[0]['state']);
    return $process;
  }

  public function test()
  {
    
    $this->getProcess(1);
  }

}