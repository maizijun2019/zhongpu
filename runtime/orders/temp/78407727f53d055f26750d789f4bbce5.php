<?php /*a:1:{s:46:"E:\Pro\pro\ZP\app\orders\view\index\form2.html";i:1597034002;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>    button {
      width: 150px;
      margin-left: 42%;
      display: disabled;
    }
    form {
      margin: 8px 50px 0 50px;
    }
    .layui-input-block {
      width: 70%;
    }
    label {
      font-size: 13px;
    }
    button {
      margin-bottom: 50px;
    }
    div.layui-form-item {
      margin-top: 27px;
    }
    div.layui-form-item:first-child {
      margin-top: 20px;
    }
    input, textarea {
      margin-left: 8px;
    }
    #remarks {
      margin: 0;
      padding: 0;
      margin-top: 20px;
    }
    select {
      width: 200px;
    }
    p {
      display: none;
      color: red;
      font-size: 13px;
    }
    .addUserBtn {
      width: 35px;
      margin: 0;
      margin-left: 10px;
      margin-top: 20px;
    }
    .user {
      width: 88%;
      float: left;
      margin-top: 15px;
    }

  </style></head><body><form class="layui-form" action="<?php echo url('place'); ?>" method="post" ><?php echo token_field(); ?><div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief"><ul class="layui-tab-title"><li class="layui-this">基本信息</li><li>提成信息</li><li>补助和服务费</li><li>时间</li><li>发票</li><li>备注</li></ul><div class="layui-tab-content"><!-- 基本信息填写 --><div class="layui-tab-item layui-show"><div class="layui-form-item"><label class="layui-form-label">项目名称 </label><div class="layui-input-block"><input   type="text" id="project" required  lay-verify="required" name="project_id"
              placeholder="请输入项目名称" autocomplete="off" class="layui-input toCheck"><p class="error">输入的项目不存在</p></div></div><div class="layui-form-item"><label class="layui-form-label">企业名称 </label><div class="layui-input-block"><input  type="text" id="enterprise" name="enterprise_id" required  lay-verify="required" 
              placeholder="请输入企业名称" autocomplete="off" class="layui-input toCheck"><p class="error">输入的企业不存在</p></div></div><div class="layui-form-item"><label class="layui-form-label">接单人 </label><div class="layui-input-block" class="userContainer"><input type="text" id="user1" name="receiving_user_ids[]" required  lay-verify="required" 
              placeholder="请输入接单人" autocomplete="off" class="layui-input toCheck user"><button type="button" class="layui-btn layui-btn-sm addUserBtn"  title="请输入接单人"><i class="layui-icon">&#xe654;</i></button><p class="error userError1">输入的用户不存在</p><p class="error">用户已输入</p></div></div><div class="layui-form-item"><label class="layui-form-label">负责人 </label><div class="layui-input-block" class="userContainer"><input  type="text" id="user2" name="responsible_user_ids[]" required  lay-verify="required" 
              placeholder="请输入负责人" autocomplete="off" class="layui-input toCheck user"><button type="button" class="layui-btn layui-btn-sm addUserBtn"  title="请输入负责人"><i class="layui-icon">&#xe654;</i></button><p class="error userError2">输入的用户不存在</p><p class="error">用户已输入</p></div></div><div class="layui-form-item"><label class="layui-form-label">部门</label><div class="layui-input-block"><select id="department" name="department" required class="layui-select"><option value="">请选择部门</option><option value="PROJECT" >项目部</option><option value="DICHOTOMY">两化部</option><option value="KNOWLEDGE">知识产权部</option><option value="FINANCE">财务部</option></select></div></div></div><!-- 提成信息填写 --><div class="layui-tab-item"><div class="layui-form-item"><label class="layui-form-label">接单人提成(%)</label><div class="layui-input-block"><input type="number" name="receiving_commission" value="10" required  
              lay-verify="required" placeholder="请输入接单人提成" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">负责人提成(%)</label><div class="layui-input-block"><input type="number" name="responsible_commission" value="8" required  lay-verify="required" 
              placeholder="请输入负责人提成" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">知识产权提成金额(￥)</label><div class="layui-input-block"><input type="number" name="total_commission" value="0" required  lay-verify="required" 
              placeholder="请输入知识产权提成金额" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">订单数量</label><div class="layui-input-block"><input type="number" name="order_number" value="1" required  lay-verify="required" 
              placeholder="请输入订单数量" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">付款方式</label><div class="layui-input-block"><input value="aaa" type="text" name="payment_method"  required  lay-verify="required" 
              placeholder="请输入付款方式" autocomplete="off" class="layui-input"></div></div></div><!-- 补助和服务费填写 --><div class="layui-tab-item"><div class="layui-form-item"><label class="layui-form-label">公示补助额度</label><div class="layui-input-block"><input type="number" name="publicity_subsidy" value="0" required  lay-verify="required" 
              placeholder="请输入公示补助额度" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">首期补助下达</label><div class="layui-input-block"><input type="text" name="first_subsidy" value="0" required  lay-verify="required" 
              placeholder="请输入首期补助下达" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">首期服务费</label><div class="layui-input-block"><input type="number" name="first_service_cost" value="0" required  lay-verify="required" 
              placeholder="请输入首期服务费" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">第二期补助下达</label><div class="layui-input-block"><input type="number" name="second_subsidy" value="0" required  lay-verify="required" 
              placeholder="请输入第二期补助下达" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">第二期服务费</label><div class="layui-input-block"><input type="number" name="second_service_cost" value="0" required  lay-verify="required" 
              placeholder="请输入第二期服务费" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">第三期补助下达</label><div class="layui-input-block"><input type="number" name="third_subsidy" value="0" required  lay-verify="required" 
              placeholder="请输入第三期补助下达" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">第三期服务费</label><div class="layui-input-block"><input type="number" name="third_service_cost" value="0" required  lay-verify="required" 
              placeholder="请输入第三期服务费" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">合同签约额(含第三方)</label><div class="layui-input-block"><input type="number" name="contract_subsidy" value="0"  required  lay-verify="required" 
              placeholder="请输入合同签约额(含第三方)" autocomplete="off" class="layui-input"></div></div></div><!-- 时间信息填写 --><div class="layui-tab-item"><div class="layui-form-item"><label class="layui-form-label">收款时间</label><div class="layui-input-block"><input type="date" name="collection_time"   lay-verify="required" 
              placeholder="请输入收款时间" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">申报时间</label><div class="layui-input-block"><input type="date" name="declare_time"   lay-verify="required" 
              placeholder="请输入申报时间" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">公示时间</label><div class="layui-input-block"><input type="date" name="publicity_time"   lay-verify="required" 
              placeholder="请输入公示时间" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">签约时间</label><div class="layui-input-block"><input type="date" name="signing_time"   lay-verify="required" 
              placeholder="请输入签约时间" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">开票时间</label><div class="layui-input-block"><input type="date" name="invoice_time"   lay-verify="required" 
              placeholder="请输入开票时间" autocomplete="off" class="layui-input"></div></div></div><!-- 发票信息填写 --><div class="layui-tab-item"><div class="layui-form-item"><label class="layui-form-label">发票号码</label><div class="layui-input-block"><input type="text" name="invoice_number"   lay-verify="required" 
              placeholder="请输入发票号码" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">开票金额</label><div class="layui-input-block"><input type="text" name="invoice_money"   lay-verify="required" 
              placeholder="请输入开票金额" autocomplete="off" class="layui-input"></div></div><div class="layui-form-item layui-form-text"><label class="layui-form-label">发票内容</label><div class="layui-input-block"><textarea name="invoice_content" placeholder="请输入发票内容" class="layui-textarea"></textarea></div></div></div><!-- 备注填写 --><div class="layui-tab-item"><div class="layui-form-item layui-form-text"><div class="layui-input-block"><textarea name="remarks" id="remarks" placeholder="请输入备注" class="layui-textarea"></textarea></div></div></div></div></div></form><button class="layui-btn layui-btn-checked" type='submit'>下单</button><script>  $(function() {
    $('select').show();
    // 增加用户 新增用户id用 'user + 数字' 表示
    i = 3;
    $('.addUserBtn').click(function() {
      var placeholder = this.title
      $(this).parent().append('<input type="text" name="responsible_user_ids[]" required  ' +
        'lay-verify="required" placeholder="' + placeholder + '" autocomplete="off"' +
        ' class="layui-input toCheck user" id=user' + i + '>')
          .append('<button type="button" id="delete' + i + '" class="layui-btn layui-btn-primary layui-btn-sm addUserBtn">' + 
              '<i class="layui-icon">&#xe640;</i></button>')
          .append('<p style="text-indent:10px" class="error userError' + i + '">用户不存在</p>')
      $('#user'+i).blur(checkName)
      $("#delete" + i).click(function() {
        $(this).prev().remove()
        $(this).remove()
      })
      i++
    });
    
    // 验证输入的项目和企业在数据库中是否存在
    function checkName() {
      $ele = $(this)
      name = this.value
      type =$ele.attr('id')
      id = type[type.length - 1]
      // 如果是用户 id去除后面的数字
      while (! isNaN(type[type.length - 1])) {
        type =type.slice(0, type.length - 1)
      }
      $.ajax({
        url: '<?php echo url("check"); ?>',
        method: 'post',
        data: {type:type, name:name},
        success: function(data) {
          if (! isNaN(id)) { // 用户查询
              $errMsg = $('.userError' + id)
          } else {
            $errMsg = $ele.next('p.error');
          }
          if (data == 0) { //未查询到数据
            $errMsg.show();
          } else {
            $errMsg.hide();
          }
        },
        error: function() {
          layer.msg("未查询到相关数据");
        }
      })
    }
    $('.toCheck').blur(checkName)
    // 提交时验证数据
    function validate() {
      checkName();
    }
    // 提交订单
    $('button[type=submit]').click(function() {
      $('form').submit();
    })
  })
</script></body></html>