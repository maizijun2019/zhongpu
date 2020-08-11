<?php /*a:1:{s:45:"E:\Pro\pro\ZP\app\orders\view\index\form.html";i:1597045965;}*/ ?>
<form class="layui-form layui-card" action="<?php echo request()->url(); ?>" data-auto="true" method="post" autocomplete="off"><div class="layui-card-body"><?php if(empty($vo['project_name']) || (($vo['project_name'] instanceof \think\Collection || $vo['project_name'] instanceof \think\Paginator ) && $vo['project_name']->isEmpty())): ?><div class="layui-form-item"><label class="layui-form-label label-required-next">项目搜索</label><div class="layui-input-block"><input id="project_name_search" class="layui-input"><button class="layui-btn" onclick="searchProject()" type='button'>搜索</button></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">项目名称</label><div class="layui-input-block"><select id="project_add" name="project_id" required class="layui-select"><option value="0">请搜索项目</option></select><p class="help-block">必选，保存后不可修改</p></div></div><?php else: ?><div class="layui-form-item"><label class="layui-form-label label-required-next">项目名称</label><div class="layui-input-block"><input value="<?php echo htmlentities((isset($vo['project_name']) && ($vo['project_name'] !== '')?$vo['project_name']:'')); ?>" required readonly class="layui-input"><p class="help-block">不可更改项</p></div></div><?php endif; if(empty($vo['enterprise_name']) || (($vo['enterprise_name'] instanceof \think\Collection || $vo['enterprise_name'] instanceof \think\Paginator ) && $vo['enterprise_name']->isEmpty())): ?><div class="layui-form-item"><label class="layui-form-label label-required-next">企业搜索</label><div class="layui-input-block"><input id="enterprise_name_search" class="layui-input"><button class="layui-btn" onclick="searchEnterprise()" type='button'>搜索</button></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">企业名称</label><div class="layui-input-block"><select id="enterprise_add" name="enterprise_id" required class="layui-select"><option value="0">请搜索企业</option></select><p class="help-block">必选，保存后不可修改</p></div></div><?php else: ?><div class="layui-form-item"><label class="layui-form-label label-required-next">企业名称</label><div class="layui-input-block"><input value="<?php echo htmlentities((isset($vo['enterprise_name']) && ($vo['enterprise_name'] !== '')?$vo['enterprise_name']:'')); ?>" required readonly class="layui-input"><p class="help-block">不可更改项</p></div></div><?php endif; ?><div class="layui-form-item"><label class="layui-form-label label-required-next">用户搜索</label><div class="layui-input-block"><input id="username" class="layui-input"><button class="layui-btn" onclick="searchUser()" type='button'>搜索</button></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">负责人</label><div class="layui-input-block"><select id="responsible_user" required class="layui-select"><option>请搜索用户</option></select><p class="help-block">必选，保存后不可修改</p><button class="layui-btn" onclick="addUser()" type='button'>添加</button></div></div><div class="layui-form-item"><label class="layui-form-label">负责人列表</label><div class="layui-inline"><div class="layui-upload-list" id="responsible_user_preview"><table class="layui-table"><thead><tr><th>头像</th><th>用户名 : 昵称</th><th>操作</th></tr></thead><tbody id="responsible_user_list"></tbody></table></div></div></div><div class="layui-form-item"><label class="layui-form-label label-required-next">部门</label><div class="layui-input-block"><select id="department_form" name="department" required class="layui-select"><option value="">请选择部门</option><option value="PROJECT">项目部</option><option value="DICHOTOMY">两化部</option><option value="KNOWLEDGE">知识产权部</option><option value="FINANCE">财务部</option></select></div></div><div class="layui-form-item"><label class="layui-form-label">接单人提成(%)</label><div class="layui-input-block"><input type="number" name="receiving_commission" value="<?php echo htmlentities((isset($vo['receiving_commission']) && ($vo['receiving_commission'] !== '')?$vo['receiving_commission']:10)); ?>" required class="layui-input"><p class="help-block">必填，值 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">负责人提成(%)</label><div class="layui-input-block"><input type="number" name="responsible_commission" value="<?php echo htmlentities((isset($vo['responsible_commission']) && ($vo['responsible_commission'] !== '')?$vo['responsible_commission']:8)); ?>" required class="layui-input"><p class="help-block">必填，值 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">知识产权提成金额(￥)</label><div class="layui-input-block"><input type="number" name="total_commissio" value="<?php echo htmlentities((isset($vo['total_commissio']) && ($vo['total_commissio'] !== '')?$vo['total_commissio']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">付款方式</label><div class="layui-input-block"><input name="payment_method" value="<?php echo htmlentities((isset($vo['payment_method']) && ($vo['payment_method'] !== '')?$vo['payment_method']:'')); ?>" required class="layui-input"><p class="help-block">必填，请选择付款方式</p></div></div><div class="layui-form-item"><label class="layui-form-label">公示补助额度</label><div class="layui-input-block"><input name="publicity_subsidy" type="number" value="<?php echo htmlentities((isset($vo['publicity_subsidy']) && ($vo['publicity_subsidy'] !== '')?$vo['publicity_subsidy']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">首期补助下达</label><div class="layui-input-block"><input name="first_subsidy" type="number" value="<?php echo htmlentities((isset($vo['first_subsidy']) && ($vo['first_subsidy'] !== '')?$vo['first_subsidy']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">首期服务费</label><div class="layui-input-block"><input name="first_service_cost" type="number" value="<?php echo htmlentities((isset($vo['first_service_cost']) && ($vo['first_service_cost'] !== '')?$vo['first_service_cost']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">第二期补助下达</label><div class="layui-input-block"><input name="second_subsidy" type="number" value="<?php echo htmlentities((isset($vo['second_subsidy']) && ($vo['second_subsidy'] !== '')?$vo['second_subsidy']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">第二期服务费</label><div class="layui-input-block"><input name="second_service_cost" type="number" value="<?php echo htmlentities((isset($vo['second_service_cost']) && ($vo['second_service_cost'] !== '')?$vo['second_service_cost']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">第三期补助下达</label><div class="layui-input-block"><input name="third_subsidy" type="number" value="<?php echo htmlentities((isset($vo['third_subsidy']) && ($vo['third_subsidy'] !== '')?$vo['third_subsidy']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">第三期服务费</label><div class="layui-input-block"><input name="third_service_cost" type="number" value="<?php echo htmlentities((isset($vo['third_service_cost']) && ($vo['third_service_cost'] !== '')?$vo['third_service_cost']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">合同签约额(含第三方)</label><div class="layui-input-block"><input name="contract_subsidy" type="number" value="<?php echo htmlentities((isset($vo['contract_subsidy']) && ($vo['contract_subsidy'] !== '')?$vo['contract_subsidy']:0)); ?>" required class="layui-input"><p class="help-block">必填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">订单数量</label><div class="layui-input-block"><input name="order_number" type="number" value="<?php echo htmlentities((isset($vo['order_number']) && ($vo['order_number'] !== '')?$vo['order_number']:1)); ?>" required class="layui-input"><p class="help-block">必填，订单数量必须 >= 1的正整数</p></div></div><div class="layui-form-item"><label class="layui-form-label">收款时间</label><div class="layui-input-block"><input name="collection_time" type="date" value="<?php echo htmlentities((isset($vo['collection_time_text']) && ($vo['collection_time_text'] !== '')?$vo['collection_time_text']:'')); ?>" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">申报时间</label><div class="layui-input-block"><input name="declare_time" type="date" value="<?php echo htmlentities((isset($vo['declare_time_text']) && ($vo['declare_time_text'] !== '')?$vo['declare_time_text']:'')); ?>" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">公示时间</label><div class="layui-input-block"><input name="publicity_time" type="date" value="<?php echo htmlentities((isset($vo['publicity_time_text']) && ($vo['publicity_time_text'] !== '')?$vo['publicity_time_text']:'')); ?>" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">签约时间</label><div class="layui-input-block"><input name="signing_time" type="date" value="<?php echo htmlentities((isset($vo['signing_time_text']) && ($vo['signing_time_text'] !== '')?$vo['signing_time_text']:'')); ?>" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">开票时间</label><div class="layui-input-block"><input name="invoice_time" type="date" value="<?php echo htmlentities((isset($vo['invoice_time_text']) && ($vo['invoice_time_text'] !== '')?$vo['invoice_time_text']:'')); ?>" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">发票号码</label><div class="layui-input-block"><input name="invoice_number" value="<?php echo htmlentities((isset($vo['invoice_number']) && ($vo['invoice_number'] !== '')?$vo['invoice_number']:'')); ?>" class="layui-input"><p class="help-block">选填</p></div></div><div class="layui-form-item"><label class="layui-form-label">开票金额</label><div class="layui-input-block"><input name="invoice_money" type="number" value="<?php echo htmlentities((isset($vo['invoice_money']) && ($vo['invoice_money'] !== '')?$vo['invoice_money']:0)); ?>" class="layui-input"><p class="help-block">选填，金额 >= 0且保留两位小数，多余小数位将四舍五入</p></div></div><div class="layui-form-item"><label class="layui-form-label">开票内容</label><div class="layui-input-block"><textarea name="invoice_content" class="layui-textarea" placeholder="开票内容,非必填项,点击右下角可进行拉伸"><?php echo htmlentities((isset($vo['invoice_content']) && ($vo['invoice_content'] !== '')?$vo['invoice_content']:'')); ?></textarea></div></div><div class="layui-form-item"><label class="layui-form-label">备注</label><div class="layui-input-block"><textarea name="remarks" class="layui-textarea" placeholder="备注,非必填项,点击右下角可进行拉伸"><?php echo htmlentities((isset($vo['remarks']) && ($vo['remarks'] !== '')?$vo['remarks']:'')); ?></textarea></div></div><?php if($COMPOSE): ?><div class="layui-form-item"><label class="layui-form-label">文件上传</label><div class="layui-inline"><button type="button" class="layui-btn" id="uploadFile"><i class="layui-icon">&#xe67c;</i>文件上传
                </button><div class="layui-upload-list" id="upload_preview"><table class="layui-table"><thead><tr><th>文件名</th><th>预览(仅支持图片预览)</th><th>操作</th></tr></thead><tbody id="imgList"></tbody></table></div></div></div><?php endif; if($PUBLICITY): ?><div class="layui-form-item"><label class="layui-form-label">公示结果</label><div class="layui-input-block"><textarea name="publicity_result" class="layui-textarea" placeholder="非必填项,点击右下角可进行拉伸"><?php echo htmlentities((isset($vo['publicity_result']) && ($vo['publicity_result'] !== '')?$vo['publicity_result']:'')); ?></textarea></div></div><?php endif; ?></div><div class="hr-line-dashed"></div><?php if(!(empty($vo['orders_id']) || (($vo['orders_id'] instanceof \think\Collection || $vo['orders_id'] instanceof \think\Paginator ) && $vo['orders_id']->isEmpty()))): ?><input type='hidden' value='<?php echo htmlentities($vo['orders_id']); ?>' name='orders_id'><?php endif; ?><div class="layui-form-item text-center"><button class="layui-btn" type='submit'>保存数据</button><button class="layui-btn layui-btn-danger" type='button' data-confirm="确定要取消编辑吗？" data-close>取消编辑</button></div></form><script type="text/javascript">    "<?php if(!(empty($vo['department']) || (($vo['department'] instanceof \think\Collection || $vo['department'] instanceof \think\Paginator ) && $vo['department']->isEmpty()))): ?>"
    var departmentElement = document.getElementById("department_form");
    for(var i = 0;i < departmentElement.length;i++){
        if("<?php echo htmlentities($vo['department']); ?>" == departmentElement[i].value){
            departmentElement[i].selected = true;
            break;
        }
    }
    "<?php endif; ?>"

    "<?php if(!(empty($vo['img_file_path']) || (($vo['img_file_path'] instanceof \think\Collection || $vo['img_file_path'] instanceof \think\Paginator ) && $vo['img_file_path']->isEmpty()))): ?>"
    var img_file_path = <?php echo json_encode($vo['img_file_path']); ?>;
    for(var i = 0;i < img_file_path.length;i++){
        $("#imgList").append("<tr>" +
            "<td>" + img_file_path[i].preview_name + "</td>" +
            "<td><img alt='预览图' width='40px' height='40px' src='"+ img_file_path[i].path +"' class='text-top margin-right-10' data-tips-image/></td>" +
            "<td>" +
            "<a class='layui-btn layui-btn-xs' target='_blank' data-title='网页预览' href='" + img_file_path[i].path + "'>网页预览</a>" +
            "<a class='layui-btn layui-btn-xs' target='_blank' data-title='下载' download href='" + img_file_path[i].path + "'>下载</a>" +
            "<button type='button' onclick='deleteImg(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
            "</td>" +
            "<input type='hidden' name='img_file_path[" + $("#imgList").children().length + "][name]' value='" + img_file_path[i].name + "'>" +
            "<input type='hidden' name='img_file_path[" + $("#imgList").children().length + "][path]' value='" + img_file_path[i].path + "'>" +
            "</tr>");
    }
    "<?php endif; ?>"

    layui.use('upload', function(){
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#uploadFile' //绑定元素
            ,url: 'api/upload/index' //上传接口
            ,multiple:true
            ,accept:"file"
            ,done: function(res){
                if (res.code == 1) {
                    layer.msg("上传成功");
                    $("#imgList").append("<tr>" +
                        "<td>" + res.preview_name + "</td>" +
                        "<td><img alt='预览图' width='40px' height='40px' src='"+ res.path +"' class='text-top margin-right-10' data-tips-image/></td>" +
                        "<td>" +
                        "<a class='layui-btn layui-btn-xs' target='_blank' data-title='网页预览' href='" + res.path + "'>网页预览</a>" +
                        "<a class='layui-btn layui-btn-xs' target='_blank' data-title='下载' download href='" + res.path + "'>下载</a>" +
                        "<button type='button' onclick='deleteImg(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
                        "</td>" +
                        "<input type='hidden' name='img_file_path[" + $("#imgList").children().length + "][name]' value='" + res.name + "'>" +
                        "<input type='hidden' name='img_file_path[" + $("#imgList").children().length + "][path]' value='" + res.path + "'>" +
                        "</tr>");
                    form.render();
                }
            },error: function(){
                //请求异常回调
            }
        });
    });

    function deleteImg(e){
        e.parentNode.parentNode.remove();
        var tr = $("#imgList").children("tr");
        for(var i = 0;i < tr.length;i++){
            var input = $(tr[i]).find("input");
            $(input[0]).attr("name","img_file_path[" + i + "][name]");
            $(input[1]).attr("name","img_file_path[" + i + "][path]");
        }
    }

    "<?php if(!(empty($vo['responsible_user_ids']) || (($vo['responsible_user_ids'] instanceof \think\Collection || $vo['responsible_user_ids'] instanceof \think\Paginator ) && $vo['responsible_user_ids']->isEmpty()))): ?>"
    var responsible_user_ids = <?php echo json_encode($vo['responsible_user_ids']); ?>;
    var whereIn = ["id",responsible_user_ids];
    $.ajax({
        url:"<?php echo url('api/query/systemUserArray'); ?>",
        method:"POST",
        data:{
            "whereIn":whereIn
        },
        success:function(data){
            data = JSON.parse(data);
            for(var i = 0;i < data.length;i++){
                $("#responsible_user_list").append("<tr>" +
                    "<td><img alt='预览图' width='40px' height='40px' src='" + data[i].headimg + "' class='text-top margin-right-10' data-tips-image/></td>" +
                    "<td>" + data[i].username + " : " + data[i].nickname + "</td>" +
                    "<td>" +
                    "<button type='button' onclick='deleteUser(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
                    "</td>" +
                    "<input type='hidden' name='responsible_user_ids[" + $("#responsible_user_list").children().length + "]' value='" + data[i].id + "'>" +
                    "</tr>");
            }
            form.render();
        },
        error:function(){
            layer.msg("网络错误,请检查网络");
        }
    });
    "<?php endif; ?>"

    function addUser(){
        var text = $("#responsible_user option:selected").text();
        var id = $("#responsible_user option:selected").val();
        var headImg = $("#responsible_user option:selected").attr("headImg");
        $("#responsible_user_list").append("<tr>" +
            "<td><img alt='预览图' width='40px' height='40px' src='" + headImg + "' class='text-top margin-right-10' data-tips-image/></td>" +
            "<td>" + text + "</td>" +
            "<td>" +
            "<button type='button' onclick='deleteUser(this)' class='layui-btn layui-btn-xs layui-btn-danger demo-delete'>删除</button>" +
            "</td>" +
            "<input type='hidden' name='responsible_user_ids[" + $("#responsible_user_list").children().length + "]' value='" + id + "'>" +
            "</tr>");
        form.render();
    }

    function deleteUser(e){
        e.parentNode.parentNode.remove();
        var tr = $("#responsible_user_list").children("tr");
        for(var i = 0;i < tr.length;i++){
            var input = $(tr[i]).find("input");
            $(input[0]).attr("name","responsible_user_ids[" + i + "]");
        }
    }

    function searchProject(){
        var project_name = $("#project_name_search").val();
        if(project_name == "" || project_name.length <= 0){
            layer.msg("请输入要搜索的项目名称");
            return;
        }
        var where = [["project_name","like","%" + project_name + "%"]];
            $.ajax({
                url:"<?php echo url('projectArray'); ?>",
                method:"POST",
                data:{
                    "where":where
                },
                success:function(data){
                    $("#project_add").empty();
                    data = JSON.parse(data);
                    if(data.length <= 0){$("#project_add").append("<option value='0'>未搜索到结果</option>");}
                    for(var i = 0;i < data.length;i++){
                        $("#project_add").append("<option value='" + data[i].project_id + "'>" + data[i].project_name + "</option>");
                    }
                    form.render();
                },
                error:function(){
                    layer.msg("网络错误,请检查网络");
                }
            });
        }

        function searchEnterprise(){
            var enterprise_name = $("#enterprise_name_search").val();
            if(enterprise_name == "" || enterprise_name.length <= 0){
                layer.msg("请输入要搜索的企业名称");
                return;
            }
            var where = [["enterprise_name","like","%" + enterprise_name + "%"]];
            $.ajax({
                url:"<?php echo url('enterpriseArray'); ?>",
                method:"POST",
                data:{
                    "where":where
                },
                success:function(data){
                    $("#enterprise_add").empty();
                    data = JSON.parse(data);
                    if(data.length <= 0){$("#enterprise_add").append("<option value='0'>未搜索到结果</option>");}
                    for(var i = 0;i < data.length;i++){
                        $("#enterprise_add").append("<option value='" + data[i].enterprise_id + "'>" + data[i].enterprise_name + "</option>");
                    }
                    form.render();
                },
                error:function(){
                    layer.msg("网络错误,请检查网络");
                }
            });
        }

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

    form.render();
</script>