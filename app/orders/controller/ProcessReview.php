<?php
namespace app\orders\controller;

use think\admin\Controller;
use think\facade\Db;
use app\orders\model\Order;
use app\orders\model\User;
use app\orders\model\ApprovalLog;
use app\orders\model\Project;
use app\orders\model\DepartmentOrders;
use app\orders\model\Enterprise;
use app\orders\model\OrderAuth;
use think\Request;

class ProcessReview extends Controller
{
  private $needReviewProcesses = ['公示', '结果公示']; 

  // 更新订单当前所需权限为初审权限
  public static function updateToFirstReviewer($orders_id)
  {
    $firstReviewer = OrderAuth::where('auth_en', 'firstReview')->find();
    Order::where('orders_id', $orders_id)->update(
      ['stage_reviewer' => json_decode($firstReviewer->user_ids)[0]],
    );
  }

  // 更新订单当前所需权限为审批权限
  private function updateToRatifyUser($orders_id) 
  {
    $ratifyUser = OrderAuth::where('auth_en', 'ratify')->find();
    Order::where('orders_id',$orders_id)->update(
      ['stage_reviewer' => json_decode($ratifyUser->user_ids)[0]],
    );
  }

  // 更新订单当前所需权限为主管权限
  private function updateToManagerReviewer($orders_id)
  {
    $order = Order::find($orders_id);
    $manager = $order->manager();
    Order::where('orders_id', $orders_id)->update(['stage_reviewer' => $manager]);
  }

  /**
   * 初审
   * @auth true
   * @auth menu
   * 
   */
  public function firstReview($orders_id)
  {
    $this->stageHandler($orders_id);
    $this->updateToRatifyUser($orders_id);
  }

    /**
   * 审批
   * @auth true
   * @auth menu
   */
  public function ratify($orders_id)
  {
    $this->stageHandler($orders_id);
    $this->updateToManagerReviewer($orders_id);
  }

  /**
 * 主管审核
 * @auth true
 * @auth menu
 */
  public function managerReview($orders_id, $consultant)
  {
    $this->stageHandler($orders_id);
    Order::where('orders_id', $orders_id)->update(['stage_reviewer', $consultant]);
  }

  /**
   * 咨询师审核
   * @auth true
   * @auth menu
   */
  public function consultantReview($orders_id)
  {
    $this->stageHandler($orders_id);
  }

  /**
   * 审核通过
   * @auth true
   * @menu true
   * 审核通过 阶段值+1 阶段值是审核阶段数组的索引 代表当前审核的阶段
   * 
   */
  public function agree($orders_id) {
    $order = Order::find($orders_id);
    if (! $order) {
      $this->error("该订单不存在");
    } 
    switch ($order->stage) {
      case "预下单":
        $this->advance($orders_id);
        break;
      case '初审':
        $this->firstReview($orders_id);
        break;
      case '审核':
        $this->ratify($orders_id);
        break;
      case '分配技术咨询师':
        $this->managerReview($orders_id);
        break;
      default:
        $this->consultantReview($orders_id);
    }
   
  }

  // 流程阶段处理
  private function stageHandler($orders_id)
  {
    $order = Order::find($orders_id);
    $stage = $order->stage;
    $operatorId = Session("user")['id'];
     // 阶段+1
     $nextStage = $order->getData('stage')+1;
   
     Order::where('orders_id', $orders_id)->update(['stage'=> $nextStage]);
    
     // 记录操作
     $res = ApprovalLog::insert([
         'order_id'=>$orders_id, 
         'stage' => $stage,
         'operator' => $operatorId,
         'opt_time' => time(),
       ]);
     if ($res < 0) {
       $this->error('审核失败');
     }
  
  }

  /**
     * 进入审核页面
     * @param $orders_id
     */
    public function process($orders_id)
    {
        $orderInfo = $this->getOrderInfo($orders_id);
        $this->fetch('process', $orderInfo);
    }
    /**
     * 获取订单信息
     * @param $orders_id  
     */
    private function getOrderInfo($orders_id)
    {
        $order = Order::where('orders_id', $orders_id)->find();
        if (! $order) {
            return;
        }
        $order->recieving_user_ids = \json_decode($order->recieving_user_ids);
        $project_name = Project::where('project_id', $order['project_id'])->value('project_name');
        $enterprise_name = Enterprise::where('enterprise_id', $order['enterprise_id'])->value('enterprise_name');
        $approval_user = User::find($order['approval_user_id'])->value('username');
        $needReview = 0;
        if (in_array($order['stage'], $this->needReviewProcesses)) {
            $needReview = 1;
        } 
        if ($order['state'] == "REJECT") {
            $rejectOperatorId = $order->rejectInfo[0]->user_id;
            $rejectOperator = getNickname($rejectOperatorId);
            // 获取流程记录
            $processLog = $order->getProcessLog($orders_id);
        }  
        return array(
            'order' => $order,
            'project_name'=> $project_name, 
            'enterprise_name' => $enterprise_name, 
            'approval_user' => $approval_user,
            'needReview' => $needReview,
            'rejectOperator' => isset($rejectOperator) ? $rejectOperator : null,
            'processLog' => isset($processLog) ? $processLog : null,
        );
    }

    /**
     * 驳回
     * @menu true
     * @auth true
     */
    public function reject2(Request $request) 
    {
      $orders_id = (int)$request->param('orders_id');
      $rejectReason = $request->param('rejectReason');
      $stage = $request->param('stage');
      $order = Order::find($orders_id);
      if (! $order) {
          $this->error("该订单不存在");
      }   
      // 修改订单状态
     $update = Order::where('orders_id', $orders_id)->update(['state' => 'REJECT']);
      
      // 修改驳回日志表
      $user = $this -> app -> session -> get('user'); 
      $insert = Db::table('zp_orders_reject_log')->insert([
        "user_id" => $user["id"],
        "orders_id" => $orders_id,
        "stage" => $stage,
        "reason" => $rejectReason,
        "create_date" => \time(),
        "create_time" => \time(),
      ]);
      if ($insert < 0) {
        $this->error("修改日志表失败");
      }

    }
    /**
     * 返回到指定流程
     * @menu true
     * @auth true
     */
    public function approval(Request $request)
    {
        $opt_time = $request->param('opt_time');
        $orders_id = $request->param('orders_id');
        $order = Order::find($orders_id);
        if ($order->stage == '初审') {
            $update = Order::where('orders_id', $orders_id)->update(['state'=>'', 'stage'=>0]);
            return;
        }
        return $order->backStage($orders_id, $opt_time);
    }
    /**
     * 查看审核记录
     * @param $order_id 订单编号
     */
    public function getRecord($orders_id)
    {
        try {
            $order = Order::find($orders_id);
            $stage = $order->stage;
            $logInfo = $order->getProcessLog($orders_id); 
            $rejectInfo = $order->rejectInfo;
        } catch (Exception $e) {
            $this->error("没有获得相关记录");
        }

        $this->fetch('record', [
            'stage' => $stage,
            'logInfo' => $logInfo,
            'rejectInfo' => $rejectInfo,
        ]);
         
    }

    /**
     * 完成审核
     * @auth true
     * @menu true
     * @param $order_id
     */
    public function finish($orders_id) 
    {
        Order::where('orders_id', $orders_id)->update(['state' => 'FINISH']);
    }

    public function test(){
    $this->updateToFirstReviewer(1);
    }
}