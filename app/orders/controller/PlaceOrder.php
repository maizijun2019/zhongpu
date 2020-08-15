<?php
namespace app\orders\controller;
use think\admin\Controller;

class PlaceOrder extends Controller
{
   // 核对输入的项目名 企业名是存在数据库
   public function check()
   {
       $type = request()->param('type');
       $name = request()->param('name');
       switch ($type) {
           case 'user':
               $res = User::where('username', $name)->find();
               break;
           case 'project':
               $res = Project::where('project_name', $name)->find();
               break;
           case 'enterprise':
               $res = Enterprise::where('enterprise_name', $name)->find();
               break;
       }
       return is_null($res) ? 0 : 1;
   }

   /**
    * 下单
    * @auth true
    * @menu true
    */
   public function place(Request $request)
   {
       $check = $request->checkToken('__token__', $request->param());
       if(false === $check) {
           throw new ValidateException('invalid token');
       }
       $formData = \request()->param();
       unset($formData['__token__']);
       // 获取项目 企业 用户 id
       $formData['project_id'] = Project::where('project_name', $formData['project_id'])->value('project_id');
       $formData['enterprise_id'] = Enterprise::where('enterprise_name', $formData['enterprise_id'])->value('enterprise_id');

       // 商务人员id
       for ($i = 0; $i < count($formData['receiving_user_ids']); $i++) {
           $userId = User::where('username', $formData['receiving_user_ids'][$i])->value('id');
           $formData['receiving_user_ids'][$i] = $userId;
       }
       $formData['receiving_user_ids'] = json_encode($formData['receiving_user_ids']);
       
       // 创建时间
       $formData['create_time'] = time();
       $formData['stage'] = 0; 

       $validate = new OrderValidate;
       $res =$validate->check($formData);

       if ($res) {
           $id = Order::insertGetId($formData);
           ProcessReview::updateToFirstReviewer($id);
           $this->redirect('/admin.html#/orders/index/index.html');
           // $this->redirect('/zp/public/index.php/admin.html#/zp/public/index.php/orders/index/index');
       } else {
           echo $validate->getError();
       }
    
   }
}