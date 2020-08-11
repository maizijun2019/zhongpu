<?php /*a:3:{s:49:"E:\Pro\pro\ZP\app\project\view\project\index.html";i:1595475123;s:40:"E:\Pro\pro\ZP\app\project\view\main.html";i:1595475123;s:56:"E:\Pro\pro\ZP\app\project\view\project\index_search.html";i:1595475123;}*/ ?>
<div class="layui-card layui-bg-gray"><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"><?php if(auth("add")): ?><button data-modal='<?php echo url("add"); ?>' data-title="添加数据" class='layui-btn layui-btn-sm layui-btn-primary'>添加数据</button><?php endif; ?></div></div><?php endif; ?><div class="layui-card-body"><div class="layui-tab layui-tab-card think-bg-white"><div class="layui-tab-content think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">项目名称</label><div class="layui-input-inline"><input name="project_name" value="<?php echo input('project_name'); ?>" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">类型</label><div class="layui-input-inline"><select id="projectLevel" name="project_level_id" class="layui-select"><option value="all">全部</option></select></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">省、市、区</label><div class="layui-input-inline"><select id="province"  name="province_region_id" lay-filter="province_add" class="layui-select"><option value="all">请选择省</option></select></div><div class="layui-input-inline"><select id="city"  name="city_region_id" lay-filter="city_add" class="layui-select"><option value="all">请选择市</option></select></div><div class="layui-input-inline"><select id="area"  name="area_region_id" class="layui-select"><option value="all">请选择区</option></select></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><script>    $.ajax({
        url:"<?php echo url('levelArray'); ?>",
        method:"GET",
        success:function(data){
            data = JSON.parse(data);
            var project_level_id = "<?php echo input('get.project_level_id'); ?>";
            var select = "";
            for(var i = 0;i < data.length;i++){
                select = "";
                if(project_level_id > 0 && project_level_id == data[i]["project_level_id"]){
                    select = "selected";
                }
                $("#projectLevel").append("<option " + select + " value='" + data[i]["project_level_id"] + "'>" + data[i]["spl"] + data[i]["title"] + "</option>");
            }
            form.render();
        }
    });

    var province_region_id = "<?php echo input('province_region_id'); ?>";
    $.ajax({
        url:"<?php echo url('region/index/province'); ?>",
        method:"GET",
        success:function(data){
            data = JSON.parse(data);
            var select = "";
            for(var i = 0;i < data.length;i++){
                select = "";
                if(province_region_id != "" && province_region_id == data[i]["region_id"]){
                    select = "selected";
                }
                $("#province").append("<option " + select + " value='" + data[i]["region_id"] + "'>" + data[i]["name"] + "</option>");
            }
            form.render();
        }
    });

    if(province_region_id > 0){
        var city_region_id = "<?php echo input('city_region_id'); ?>";
        $.ajax({
            url:"<?php echo url('region/index/city'); ?>?province_region_id=" + province_region_id,
            method:"GET",
            success:function(data){
                data = JSON.parse(data);
                var select = "";
                for(var i = 0;i < data.length;i++){
                    select = "";
                    if(city_region_id != "" && city_region_id == data[i]["region_id"]){
                        select = "selected";
                    }
                    $("#city").append("<option " + select + " value='" + data[i]["region_id"] + "'>" + data[i]["name"] + "</option>");
                }
                form.render();
            }
        });

        if(city_region_id > 0){
            var area_region_id = "<?php echo input('area_region_id'); ?>";
            $.ajax({
                url:"<?php echo url('region/index/area'); ?>?city_region_id=" + city_region_id,
                method:"GET",
                success:function(data){
                    data = JSON.parse(data);
                    var select = "";
                    for(var i = 0;i < data.length;i++){
                        select = "";
                        if(area_region_id != "" && area_region_id == data[i]["region_id"]){
                            select = "selected";
                        }
                        $("#area").append("<option " + select + " value='" + data[i]["region_id"] + "'>" + data[i]["name"] + "</option>");
                    }
                    form.render();
                }
            });
        }
    }

    form.render();

    form.on('select(province_add)', function(data){
        $("#city").empty();
        $("#city").append("<option value='all'>请选择市</option>");
        $("#area").empty();
        $("#area").append("<option value='all'>请选择区</option>");
        if(data.value == "all"){
            form.render();
            return;
        }
        $.ajax({
            url:"<?php echo url('region/index/city'); ?>?province_region_id=" + data.value,
            method:"GET",
            success:function(data){
                data = JSON.parse(data);
                for(var i = 0;i < data.length;i++){
                    $("#city").append("<option value='" + data[i]["region_id"] + "'>" + data[i]["name"] + "</option>");
                }
                form.render();
            }
        });
    });

    form.on('select(city_add)', function(data){
        $("#area").empty();
        $("#area").append("<option value='all'>请选择区</option>");
        if(data.value == "all"){
            form.render();
            return;
        }
        $.ajax({
            url:"<?php echo url('region/index/area'); ?>?city_region_id=" + data.value,
            method:"GET",
            success:function(data){
                data = JSON.parse(data);
                for(var i = 0;i < data.length;i++){
                    $("#area").append("<option value='" + data[i]["region_id"] + "'>" + data[i]["name"] + "</option>");
                }
                form.render();
            }
        });
    });
</script><table class="layui-table margin-top-10" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-sort-td'><button type="button" data-reload class="layui-btn layui-btn-xs">刷 新</button></th><th class='text-left nowrap'>项目名称</th><th class='text-left nowrap'>分类</th><th class='text-left nowrap'>省、市、区</th><th class='text-left nowrap'>备注</th><th class='text-left nowrap'>创建时间</th><th class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr data-dbclick><td></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['project_name']) && ($vo['project_name'] !== '')?$vo['project_name']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['title']) && ($vo['title'] !== '')?$vo['title']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['province_text']) && ($vo['province_text'] !== '')?$vo['province_text']:''); ?></span><span><?php echo (isset($vo['city_text']) && ($vo['city_text'] !== '')?$vo['city_text']:''); ?></span><span><?php echo (isset($vo['area_text']) && ($vo['area_text'] !== '')?$vo['area_text']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><textarea readonly class="layui-text"><?php echo (isset($vo['remarks']) && ($vo['remarks'] !== '')?$vo['remarks']:''); ?></textarea></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['create_time_text']) && ($vo['create_time_text'] !== '')?$vo['create_time_text']:''); ?></span></div></td><td class='text-left nowrap'><?php if(auth("edit")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑" data-modal='<?php echo url("edit"); ?>?project_id=<?php echo htmlentities($vo['project_id']); ?>'>编 辑</a><?php endif; if(auth("remove")): ?><a class="layui-btn layui-btn-danger layui-btn-xs" data-confirm="确定要删除吗?" data-action="<?php echo url('remove'); ?>" data-value="project_id#<?php echo htmlentities($vo['project_id']); ?>" data-csrf="<?php echo systoken('remove'); ?>">删 除</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div></div>