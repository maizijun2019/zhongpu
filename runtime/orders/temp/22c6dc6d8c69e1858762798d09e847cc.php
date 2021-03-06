<?php /*a:1:{s:44:"E:\Pro\pro\ZP\app\orders\view\auth\form.html";i:1596970438;}*/ ?>
<form class="layui-form layui-card" action="<?php echo request()->url(); ?>" data-auto="true" method="post" autocomplete="off"><div class="layui-card-body"><div class="layui-form-item"><label class="layui-form-label label-required-next">权限名称</label><div class="layui-input-block"><input name="auth_name" value="<?php echo htmlentities((isset($vo['auth_name']) && ($vo['auth_name'] !== '')?$vo['auth_name']:'')); ?>" placeholder="请输入权限名称，建议不超过7个字符" required class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">拥有权限</label><div class="layui-input-block"><input type="checkbox" id="auth_base" name="auth[base]" title="基本进度"><input type="checkbox" id="auth_approval" name="auth[approval]" title="审批"><input type="checkbox" id="auth_upload" name="auth[upload]" title="上传"></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">用户搜索</label><div class="layui-input-block"><input id="username" class="layui-input"><button class="layui-btn" onclick="searchUser()" type='button'>搜索</button></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">技术咨询师</label><div class="layui-input-block"><select id="responsible_user" required class="layui-select"><option>请搜索用户</option></select><p class="help-block">必选，保存后不可修改</p><button class="layui-btn" onclick="addUser()" type='button'>添加</button></div></div><div class="layui-form-item"><label class="layui-form-label">技术咨询师列表</label><div class="layui-inline"><div class="layui-upload-list" id="responsible_user_preview"><table class="layui-table"><thead><tr><th>头像</th><th>用户名 : 昵称</th><th>操作</th></tr></thead><tbody id="responsible_user_list"></tbody></table></div></div></div></div><div class="hr-line-dashed"></div><?php if(!(empty($vo['orders_auth_id']) || (($vo['orders_auth_id'] instanceof \think\Collection || $vo['orders_auth_id'] instanceof \think\Paginator ) && $vo['orders_auth_id']->isEmpty()))): ?><input type='hidden' value='<?php echo htmlentities($vo['orders_auth_id']); ?>' name='orders_auth_id'><?php endif; ?><div class="layui-form-item text-center"><button class="layui-btn" type='submit'>保存数据</button><button class="layui-btn layui-btn-danger" type='button' data-confirm="确定要取消编辑吗？" data-close>取消编辑</button></div></form><script>    "<?php if(!(empty($vo['user_ids']) || (($vo['user_ids'] instanceof \think\Collection || $vo['user_ids'] instanceof \think\Paginator ) && $vo['user_ids']->isEmpty()))): ?>"
    var user_ids = '<?php echo json_encode($vo['user_ids']); ?>';
    var whereIn = ["id",user_ids];
    $.ajax({
        url:"<?php echo url('api/query/systemUserArray'); ?>",
        method:"POST",
        data:{
            "whereIn":whereIn
        },
        success:function(data){
            data = JSON.parse(data);
            for(var i = 0;i < data.length;i++){
                var user_ids_id = "user_ids_" + data[i].id;
                if(document.getElementById(user_ids_id)){
                    layer.msg("该用户已添加过");
                    return;
                }
                $("#responsible_user_list").append("<tr>" +
                    "<td><img alt='预览图' width='40px' height='40px' src='" + data[i].headimg + "' class='text-top margin-right-10' data-tips-image/></td>" +
                    "<td>" + data[i].username + " : " + data[i].nickname + "</td>" +
                    "<td>" +
                    "<button type='button' onclick='deleteUser(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
                    "</td>" +
                    "<input type='hidden' id='" + user_ids_id + "' name='user_ids[" + $("#responsible_user_list").children().length + "]' value='" + data[i].id + "'>" +
                    "</tr>");
            }
            form.render();
        },
        error:function(){
            layer.msg("网络错误,请检查网络");
        }
    });
    "<?php endif; ?>"

    "<?php if(!(empty($vo['auth']) || (($vo['auth'] instanceof \think\Collection || $vo['auth'] instanceof \think\Paginator ) && $vo['auth']->isEmpty()))): ?>"
    var auth = JSON.parse('<?php echo json_encode($vo['auth']); ?>');
    for(var i = 0;i < auth.length;i++){
        $("#auth_" + auth[i]).prop("checked",true);
    }
    "<?php endif; ?>"

    function searchUser(){
        var username = $("#username").val();
        if(username == "" || username.length <= 0){
            layer.msg("请输入要搜索的用户名");
            return;
        }
        var where = [["username","like","%" + username + "%"]];
        $.ajax({
            url:"<?php echo url('api/query/systemUserArray'); ?>",
            method:"POST",
            data:{
                "where":where
            },
            success:function(data){
                $("#responsible_user").empty();
                data = JSON.parse(data);
                if(data.length <= 0){$("#responsible_user").append("<option value='0'>未搜索到结果</option>");}
                for(var i = 0;i < data.length;i++){
                    $("#responsible_user").append("<option headImg='" + data[i].headimg + "' value='" + data[i].id + "'>" + data[i].username + " : " + data[i].nickname + "</option>");
                }
                form.render();
            },
            error:function(){
                layer.msg("网络错误,请检查网络");
            }
        });
    }

    function addUser(){
        var text = $("#responsible_user option:selected").text();
        var id = $("#responsible_user option:selected").val();
        var headImg = $("#responsible_user option:selected").attr("headImg");
        var user_ids_id = "user_ids_" + id;
        if(document.getElementById(user_ids_id)){
            layer.msg("该用户已添加过");
            return;
        }
        $("#responsible_user_list").append("<tr>" +
            "<td><img alt='预览图' width='40px' height='40px' src='" + headImg + "' class='text-top margin-right-10' data-tips-image/></td>" +
            "<td>" + text + "</td>" +
            "<td>" +
            "<button type='button' onclick='deleteUser(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
            "</td>" +
            "<input type='hidden' id='" + user_ids_id + "' name='user_ids[" + $("#responsible_user_list").children().length + "]' value='" + id + "'>" +
            "</tr>");
        form.render();
    }

    function deleteUser(e){
        e.parentNode.parentNode.remove();
        var tr = $("#responsible_user_list").children("tr");
        for(var i = 0;i < tr.length;i++){
            var input = $(tr[i]).find("input");
            $(input[0]).attr("name","user_ids[" + i + "]");
        }
    }
    form.render();
</script>