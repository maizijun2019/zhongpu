<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class OrderValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'project_id' =>'require|number',
        'enterprise_id' => 'require|number',
        'receiving_user_ids' => 'require|min:4',
        'department' => 'require|min:2|max:20',
        'order_number' => 'require|number',
        'receiving_commission' => 'require|number',
        'responsible_commission' => 'require|number',
        'total_commission' => 'require|number',
        'payment_method' => 'require',
        'publicity_subsidy' => 'require',
        'first_subsidy' => 'require',
        'first_service_cost' => 'require',
        'second_subsidy' => 'require',
        'second_service_cost' => 'require',
        'third_subsidy' => 'require',
        'third_service_cost' => 'require',
        'contract_subsidy' => 'require',
        'collection_time' => 'date',
        'signing_time' => 'date',
        'publicity_time' => 'date',
        'declare_time' => 'date',
        'invoice_time' => 'date',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'project_id.require' => '项目名称必须填写',
        'department_id.require' => '部门名称必须填写',
    ];
}
