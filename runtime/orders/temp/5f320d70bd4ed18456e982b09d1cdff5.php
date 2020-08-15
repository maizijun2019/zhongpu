<?php /*a:1:{s:47:"E:\Pro\pro\ZP\app\orders\view\index\record.html";i:1597387725;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>    table.table, .processContainer {
      width: 85%;
      margin: 10px auto 40px;
    }
    table caption {
      font-size: 16px;
      margin-bottom: 10px;
    }
    .process {
      font-size: 14px;
      text-indent:2em;
      color: rgb(153, 118, 112);
    }
 
    .processContainer p 
    {
      font-size: 16px;
      text-align: center;
      color: rgb(94, 92, 92);
    }
  </style></head><body><input type="hidden" id="stage" name="stage" value="<?php echo htmlentities($stage); ?>"><div class="processContainer"><p>审核流程</p><hr><div class="process"><?php foreach($orderProcess as $stage): if($stage == $orderProcess[count($orderProcess) - 1]): ?><span><?php echo htmlentities($stage->name); ?></span><?php else: ?><span><?php echo htmlentities($stage->name); ?></span><span>-></span><?php endif; ?><?php endforeach; ?></div></div><hr><table class="layui-table table"><caption>通过记录</caption><colgroup><col width="150"><col width="150"><col width="150"></colgroup><thead><tr><th id="aa">流程</th><th>审核人</th><th>通过日期</th></tr></thead><tbody><?php foreach($logInfo as $record): ?><tr><td><?php echo htmlentities($record['stage']); ?></td><td><?php echo getNickname($record['operator']) ?></td><td><?php echo date('Y年m月d日', $record['opt_time']) ?></td></tr><?php endforeach; ?></tbody></table><hr><table class="layui-table table table2"><caption>驳回记录</caption><colgroup><col width="150"><col width="150"><col width="150"></colgroup><thead><tr><th id="aa">流程</th><th>审核人</th><th>驳回日期</th></tr></thead><tbody><?php foreach($rejectInfo as $record): ?><tr><td><?php echo htmlentities($record['stage']); ?></td><td><?php echo getNickname($record['user_id']) ?></td><td><?php echo $record['create_date'] ? date('Y年m月d日', $record['create_date']) : '' ?></td></tr><input id="<?php echo htmlentities($record['create_date']); ?>" type="hidden" name="rejectReason" value="<?php echo htmlentities($record['reason']); ?>"><?php endforeach; ?></tbody></table><script>    layui.use('element', function(){
      var element = layui.element;
    });
    if ($('.table2 td')) {
      $('.table2 td').click("on", function() {
        rejectReason = $(this).parent('tr').next().val()
        layer.open({
          title: '驳回理由',
          content:  rejectReason ? rejectReason : '<p style="text-align:center">未填写驳回理由</p>',
        })
      })
    }
    var stage = $('#stage').val();
    $('.process>span:contains(' + stage + ')').css("color", "red")
  </script></body></html>