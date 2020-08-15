$(function() {
  $('select').show();

  // 增加用户 新增用户id用 'user + 数字' 表示
    i = 3;
  $('.addUserBtn').click(addUser);

  function addUser() {
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
  }
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
      url: '/orders/index/check',
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
  

  // 验证
  // $('form').validate({
  //   project_id: {
  //     require: true,
  //   },
  // })
})