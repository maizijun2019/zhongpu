<?php
namespace app\orders\controller;

use think\admin\Controller;
use app\orders\model\Order;
use app\orders\model\User;
use app\orders\model\ApprovalLog;
use app\orders\model\RejectInfo;
use app\orders\model\DepartmentOrders;
use think\facade\Session;
use think\facade\View;


class OrderNavigation extends Controller
{
  private $myId;
  private $department;
  private $myOrderAuth;

  function __construct() 
  {
    $this->myId = Session::get("myId");
    $this->department = Session::get("department");
    $this->myOrderAuth = User::find($this->myId)->orders_auth_id;
  }

  // 渲染模板
  private function fetchView($list)
  {
    $this->fetch('index/orders', ['list' => $list, 'myId' => $this->myId, 'myOrderAuth'=>$this->myOrderAuth]);
  }

  // 待审核订单
  public function getWaitToApprovalOrders()
  {
    $list = Order::where('stage_reviewer', $this->myId)->select();
    $this->fetchView($list);
  }

  // 已审核订单
  public function getFinishedOrders()
  {
    $list = Order::where('state', 'FINISH')->where('department', $this->department)->select();
    $this->fetchView($list);

  }

  // 部门驳回订单
  public function getRejectOrders()
  {
    $list = Order::where('state', 'REJECT')->where('department', $this->department)->select();
    $this->fetchView($list);

  }

  //部门全部订单
  public function getDepartmentOrders()
  {
    $list =  Order::where('department', $this->department)->select();
    $this->fetchView($list);

  }

  // 全部订单
  public function getAllOrders()
  {
    $list = Order::select();
    $this->fetchView($list);

  }

  public function test()
  {
    $this->getRejectOrders();
  }
}