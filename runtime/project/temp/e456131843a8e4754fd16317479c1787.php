<?php /*a:1:{s:47:"E:\Pro\pro\ZP\app\project\view\level\index.html";i:1595475123;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Title</title></head><body><fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;"><legend>关系等级</legend></fieldset><div class="layui-collapse" id="parentNode_0" lay-accordion></div></body><script>    var list = <?php echo json_encode($list); ?>;
    subNode("parentNode_0",list);
    function subNode(parentNodeId,list){
        for(var i = 0;i < list.length;i++){
            var html = "<span class='color-desc' style='margin-left: 10%;float: right;'>" + list[i]["type_text"] + "<span style='margin-right: 75px;'></span>";
            '<?php if(auth("add")): ?>'
            html += "<a class=\"layui-btn layui-btn-xs layui-btn-primary\" data-title=\"添加子等级\" data-modal='<?php echo url("add"); ?>?pid=" + list[i]["project_level_id"] + "'>添 加</a>";
            '<?php endif; ?>'

            '<?php if(auth("attribute")): ?>'
            html += "<a class=\"layui-btn layui-btn-xs layui-btn-primary\" data-title=\"属性\" data-modal='<?php echo url("attribute"); ?>?project_level_id=" + list[i]["project_level_id"] + "'>属 性</a>";
            '<?php endif; ?>'

            '<?php if(auth("edit")): ?>'
            html += "<a data-dbclick class=\"layui-btn layui-btn-xs\" data-title=\"编辑等级\" data-modal='<?php echo url("edit"); ?>?project_level_id=" + list[i]["project_level_id"] + "'>编 辑</a>";
            '<?php endif; ?>'

            '<?php if(auth("remove")): ?>'
            html += "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" data-confirm=\"确定要删除 '" + list[i]["title"] + "' 以及子等级吗?\" data-action=\"<?php echo url('remove'); ?>\" data-value=\"project_level_id#" + list[i]["project_level_id"] + "\" data-csrf=\"<?php echo systoken('remove'); ?>\">删 除</a>";
            '<?php endif; ?>'
            html += "</span>";
            var id = "parentNode_" + list[i]["project_level_id"];
            if(list[i]["sub"].length > 0){
                $("#" + parentNodeId).append('<div class="layui-colla-item"><h2 class="layui-colla-title">' + list[i]["title"] + html + '</h2><div class="layui-colla-content"><div lay-accordion class="layui-collapse" id="' + id + '"></div></div></div>');
                subNode(id,list[i]["sub"]);
            }else{
                $("#" + parentNodeId).append('<div class="layui-colla-item"><h2 class="layui-colla-title">' + list[i]["title"] + html + '</h2><div class="layui-colla-content"><p>没有更多了</p></div></div>');
            }
        }
    }
</script><script src="/static/plugs/layui/lay/modules/element.js" charset="utf-8"></script></html>