<?php /*a:1:{s:47:"E:\Pro\pro\ZP\app\orders\view\index\record.html";i:1597019977;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>    table.table {
      width: 85%;
      margin: 10px auto 20px;
    }
    table caption {
      font-size: 16px;
      margin-bottom: 10px;
    }
  </style></head><body><table class="layui-table table"><caption>通过记录</caption><colgroup><col width="150"><col width="150"><col width="150"></colgroup><thead><tr><th id="aa">流程</th><th>审核人</th><th>通过日期</th></tr></thead><tbody><?php foreach($logInfo as $record): ?><tr><td><?php echo htmlentities($record['stage']); ?></td><td><?php echo getOperatorUsername($record['operator']) ?></td><td><?php echo date('Y年m月d日', $record['opt_time']) ?></td></tr><?php endforeach; ?></tbody></table><hr><table class="layui-table table table2"><caption>驳回记录</caption><colgroup><col width="150"><col width="150"><col width="150"></colgroup><thead><tr><th id="aa">流程</th><th>审核人</th><th>驳回日期</th></tr></thead><tbody><?php foreach($rejectInfo as $record): ?><tr><td><?php echo htmlentities($record['stage']); ?></td><td><?php echo getOperatorUserame($record['user_id']) ?></td><td><?php echo $record['create_date'] ? date('Y年m月d日', $record['create_date']) : '' ?></td></tr><input id="<?php echo htmlentities($record['create_date']); ?>" type="hidden" name="rejectReason" value="<?php echo htmlentities($record['reason']); ?>"><?php endforeach; ?></tbody></table><script>    if ($('.table2 td')) {
      $('.table2 td').click("on", function() {
        rejectReason = $(this).parent('tr').next().val()
        layer.open({
          title: '驳回理由',
          content:  rejectReason ? rejectReason : '<p style="text-align:center">未填写驳回理由</p>',
        })
      })
    }
  </script></body></html>