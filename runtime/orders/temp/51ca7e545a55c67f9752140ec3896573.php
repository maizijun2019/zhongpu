<?php /*a:1:{s:48:"E:\Pro\pro\ZP\app\orders\view\index\process.html";i:1597051185;}*/ ?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><style>    table {
      margin: 20px auto 30px;
      width: 75%;
      font-size: 14px;
    }
    table tr {
      height: 25px;
      border-bottom: 1px solid #eee;
    }
    tr td:nth-child(1) {
      width: 100px;
      text-align: center;
    }
    tr td:nth-child(2) {
      padding: 15px;
    }
    .agree, .reject {
      margin-bottom: 30px;
      width: 100px;
    }
    .agree {
      margin-left: 40%;
    }
    #upload_file {
      margin-left: 15%;
      margin-bottom: 20px;
    }
    .finish {
      text-align: center;
      font-size: 18px;
      margin-bottom: 30px;
      color: rgb(170, 29, 40);
    }
    .reject a {
      color: white;
    }
    .reject a:hover {
      color: white;
    }
    select {
      height: 30px;
      width: 40%;
    }
    .layui-btn {
      width: 20%;
    }
    button {
      letter-spacing:4px;
    }
  </style></head><body><table><tr><td>订单号：</td><td><?php echo htmlentities($order->orders_id); ?></td></tr><tr><td>审核流程：</td><td style="color:rgb(212, 53, 13)"><?php if($order['state'] == 'REJECT'): ?>          驳回(未通过)
        <?php else: ?><?php echo htmlentities($order['stage']); ?><?php endif; ?></td></tr><tr><td>项目名称：</td><td><?php echo htmlentities($project_name); ?></td></tr><tr><td>企业名称：</td><td><?php echo htmlentities($enterprise_name); ?></td></tr><?php if($order['stage'] != '初审' && $order['stage'] != '预下单' && $order['stage'] != '审核'): ?><tr><td>下单日期：</td><td><?php echo date('Y-m-d', $order->create_date) ?></td></tr><tr><td>审批日期：</td><td><?php echo htmlentities($order->approval_time); ?></td></tr><?php endif; if($order['state'] == 'FINISH'): ?><tr><td>完成日期：</td><td><?php echo htmlentities($order->approval_time); ?></td></tr><?php endif; if($order['state'] == 'REJECT'): ?><tr><td>驳回操作人：</td><td><?php echo htmlentities($rejectOperator); ?></td></tr><tr><td>驳回日期：</td><td><?php  echo date('Y-m-d H:i:s', $order->lastRejectInfo($order->orders_id)['create_date']) ?></td></tr><?php if($order->lastRejectInfo($order->orders_id)['reason']): ?><tr><td>驳回理由：</td><td><?php echo htmlentities($order->lastRejectInfo($order->orders_id)['reason']); ?></td></tr><?php endif; ?><tr><?php if($order['stage'] != '初审'): ?><td>返回到流程</td><td><div class=""><select name="backStage" id="backStage" lay-verify="required" class="select"><option value="">选择返回的流程</option><?php foreach($processLog as $val): if($val['stage'] != "预下单"): ?><option value=<?php echo htmlentities($val['opt_time']); ?>><?php echo htmlentities($val['stage']); ?></option><?php endif; ?><?php endforeach; ?></select></div></td><?php endif; ?></tr><?php endif; if(($order['state'] != 'REJECT') && $needReview === 1): ?><tr><td><?php if($needReview === 1): ?>未通过原因：<?php else: ?>驳回原因：<?php endif; ?></td><td><textarea id="rejectReason" name="rejectReason" placeholder="可不填写" class="layui-textarea"></textarea></td></tr><?php endif; ?></table><input id="orders_id" type="hidden" name="orders_id" value="<?php echo htmlentities($order['orders_id']); ?>"><input id="stage" type="hidden" name="stage" value="<?php echo htmlentities($order['stage']); ?>" ><?php if($order['stage'] == '申报材料撰写'): ?><button type="button" class="layui-btn layui-btn-checked" id="upload_file"><i class="layui-icon">&#xe67c;</i>上传文件
  </button><br><?php endif; if($order['state'] == 'REJECT'): ?><button class="layui-btn layui-btn-checked agree" id="approval">重新审核</button><?php else: if(! $order->getStageIndex($order->orders_id, $order['stage'])['isFinish']): ?><div class="buttons"><button class="layui-btn agree" id="agree"  
        <?php if($needReview === 1 ||  $order['stage'] == '初审' ||  $order['stage'] == '审核'): ?> style="margin-left: 30%;" 
        <?php else: ?> style="margin-left: 40%;" <?php endif; ?>>        通过</button><?php if($needReview === 1): ?><button class="layui-btn layui-btn-danger reject rejectBtn">不通过</button><?php endif; if($order['stage'] == '初审' || $order['stage'] == '审核'): ?><button class="layui-btn layui-btn-danger reject rejectBtn">驳回</button><?php endif; ?></div><?php else: if($order['state'] != 'FINISH'): ?><p id="finish" class="finish"><button class="layui-btn layui-btn-danger reject" id="finish">完成审核</button></p><?php else: ?><p class="finish">已完成审核</p><?php endif; ?><?php endif; ?><?php endif; ?></body><script>    orders_id = document.getElementById('orders_id').value
    stage = document.getElementById('stage').value
    // 点击通过
    $('#agree').on('click', function () {
        $.ajax({
        type:'get',
        // url: '/zp/public/index.php/orders/Index/agree',
        url: '<?php echo url("agree"); ?>',
        data: 'orders_id=' + orders_id + '&stage=' + stage,
        success: function (data, status) {
          console.log(data);
          // finish('审核通过');
        }
      })
    })

    // 点击驳回
    if ($(".rejectBtn").length > 0) {
      $('.rejectBtn').on('click', function () {
        rejectReason = "";
        if ($('#rejectReason').length > 0){
          rejectReason = $("#rejectReason").val();
        }
        $.ajax({
          type:'post',
          url: '/zp/public/index.php/orders/index/reject2',
          data: {orders_id:orders_id, rejectReason:rejectReason, stage:stage},
          success: function (data, status) {
            finish('已驳回');
          }
        })
      })
    }

    // 重新审核 
    if ($('#approval').length > 0) {
      $('#approval').on('click', function () {
        if ($('#backStage').length > 0) {
          opt_time = $("#backStage").val();
          if (opt_time == '') {
            layer.msg('请选择要返回的流程');
            return;
          } 
        } else {
          opt_time = 0
        }
        $.ajax({
          type:'post',
          url: '/zp/public/index.php/orders/index/approval',
          data: {orders_id: orders_id, opt_time: opt_time},
          success: function (data) {
            alert(data)
            finish('已重新修改流程');
          }
        })
      })
    }
    // if (backStageItem = document.getElementById("backStage")){
    //   $('#approval').on('click', function () {
    //       opt_time = $("#backStage").val();
    //       if (opt_time == '') {
    //         layer.msg('请选择要返回的流程');
    //         return;
    //       } 
    //       $.ajax({
    //         type:'post',
    //         url: '/orders/index/approval',
    //         data: {orders_id: orders_id, opt_time: opt_time},
    //         success: function (data, status) {
    //           finish('已重新修改流程');
    //         }
    //       })
         
    //   })
    // } else {
    //   $('#approval').on('click', function () {
    //     $.ajax({
    //       type:'post',
    //       url: '/orders/index/approval',
    //       data: {orders_id: orders_id,opt_time:0},
    //       success: function (data, status) {
    //         finish('已重新修改流程');
    //       }
    //     })
    //   })
    // }

    // 完成审核
    if ($('#finish').length > 0) {
      $('#finish').click(function() {
        $.ajax({
          type: 'get',
          url: '/zp/public/index.php/orders/index/finish',
          data: 'order_id='+orders_id,
          success: function(data) {
            layer.msg('已完成审核',{
              time:450,
            });
            layer.closeAll('page')
          }
        })
      })
    }

    // 上传文件
    if ($('#upload_file')) {
      layui.use('upload', function(){
        var upload = layui.upload;
        //执行实例
        var uploadInst = upload.render({
          elem: '#upload_file' //绑定元素
          ,url: "<?php echo url('upload'); ?>" //上传接口
          ,accept: 'file'
          ,done: function(res){
            //上传完毕回调
          }
          ,error: function(){
            //请求异常回调
          }
        });
      });
    }

    // 弹窗提示完成操作 + 退出
    function finish(content) {
      layer.alert("<div style='text-align:center'>" + content + "</div>");
      $(".layui-layer-btn0").on("click", function() {
          layer.closeAll('page')
      });
      $(".layui-layer-close").on("click", function() {
          layer.closeAll('page')
      });
    }
</script></html>