<?php
namespace app\orders\model;
use think\Model;
use think\facade\Db;
use app\orders\model\ApprovalLog;

class Order extends Model
{
  protected $table = 'zp_orders';
  protected $pk = "orders_id";
  protected $type = [
      'approval_time' => 'timestamp:Y-m-d',
      'declare_time' => 'timestamp:Y-m-d',
      'create_time' => 'timestamp:Y-m-d',
      'approval_time' =>'timestamp:Y-m-d',
  ];

  // 关联审核日志表
  public function approvalLog()
  {
    $this->hasMany(ApprovalLog::class, 'order_id', 'orders_id');
  }
  // 获取部门的中文名
  protected function getDepartmentAttr($value, $data)
  {
    $department = $data['department'];
    switch ($department) {
      case 'PROJECT':
        $department = '项目部';
        break;
      case 'DICHOTOMY':
        $department = '两化融合部';
        break;
      case 'KNOWLEDGE':
        $department = '知识产权部';
        break;
      case 'FINANCE ':
        $department = '财务部';
        break;
    }
    return $department;
  }

  // 获取流程的中文名
  protected function getStageAttr($value, $data)
  {
    $state = $data['stage'];
    $department = $data['department'];
    // 获取订单流程类型（是哪个部门的流程）
    switch ($department) {
      case 'PROJECT':
        $orders_process_id = 1;
        break;
      case 'DICHOTOMY':
        $orders_process_id = 2;
        break;
      case 'KNOWLEDGE':
        $orders_process_id = 3;
        break;
      case 'FINANCE ':
        $orders_process_id = 4;
        break;
    }
    // 流程
    $process = Db::table('zp_orders_process')->where('orders_process_id', $orders_process_id)
        ->value('state');
    // 流程中的阶段
    $stage = json_decode($process)[$state];
    return $stage->name;
  }

  // 获取订单所属项目名
  public function getProjectName($project_id)
  {
    return Db::table("zp_project")->where('project_id', $project_id)->value('project_name');
  }

  // 获取订单所属企业名
  public function getEnterpriseName($enterprise_id) 
  {
    return Db::table("zp_enterprise")->where('enterprise_id', $enterprise_id)->value('enterprise_name');
  }

  // 返回是否结算中文
  public function getSettlementAttr($value)
  {
    $settlementZh = "";
    if ($value == "Y") {
      $settlementZh = "是";
    } else if($value == "N") {
      $settlementZh = "否";
    }
    return $settlementZh;
  }

  // 获取全部驳回信息
  public function rejectInfo()
  {
    return $this->hasMany(RejectInfo::class, 'orders_id');
  }

  //获取最近的驳回信息
  public function lastRejectInfo($id)
  {
    $allRejectInfo = Order::find($id)->rejectInfo;
    $count =  $allRejectInfo->count();
    return $allRejectInfo[$count-1]->toArray();
  }

  // 获取流程日志
  public  function getProcessLog($order_id)
  {
    $logInfo = ApprovalLog::where('order_id', $order_id)
      ->where('opt_time', '<>', -1)->order("opt_time", "desc")->select();
    return $logInfo;
  }

  // 返回流程
  public  function backStage($order_id, $opt_time)
  {
    // 修改订单状态
    $stage =  ApprovalLog::where('opt_time', $opt_time)->find()['stage'];
    Order::where('orders_id', $order_id)->update([
      'state' => '',
      'stage' => $this->getStageIndex($order_id, $stage)['index'],
    ]);
    // 修改审核日志表
    $records = ApprovalLog::where('order_id', $order_id)
        ->where('opt_time', '>', $opt_time)->select()->toArray();
    foreach ($records as $record) {
      $log = ApprovalLog::where('opt_time', $record['opt_time'])->find();
      $log->opt_time = -1;
      $log->delete_time =  time();
      $log->save();
    }
  } 

  // 返回流程阶段索引
  public function getStageIndex($order_id, $stageName)
  {
    $order = Order::find($order_id);
    $department =$order->department;
    $stages = Db::table('zp_orders_process')->where('name', $department . "流程")->field('state')->find();
    $stages =  json_decode($stages['state']);
    $index = -1;
    for ($i = 0; $i < count($stages); $i++) {
      if ($stages[$i]->name == $stageName) {
        $index =  $i;
        break;
      }
    }
    return array('isFinish'=>($index == count($stages)-1 ? 1 : 0), 'index'=>$index);
  }

}