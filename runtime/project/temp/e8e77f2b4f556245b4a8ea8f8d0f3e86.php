<?php /*a:3:{s:51:"E:\Pro\pro\ZP\app\project\view\attribute\index.html";i:1595475123;s:40:"E:\Pro\pro\ZP\app\project\view\main.html";i:1595475123;s:58:"E:\Pro\pro\ZP\app\project\view\attribute\index_search.html";i:1595475123;}*/ ?>
<div class="layui-card layui-bg-gray"><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"><?php if(auth("add")): ?><button data-modal='<?php echo url("add"); ?>' data-title="添加属性" class='layui-btn layui-btn-sm layui-btn-primary'>添加属性</button><?php endif; ?></div></div><?php endif; ?><div class="layui-card-body"><div class="layui-tab layui-tab-card think-bg-white"><div class="layui-tab-content think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><!--<div class="layui-form-item layui-inline">--><!--<label class="layui-form-label">项目类型</label>--><!--<div class="layui-input-inline">--><!--<select id="levelArray" name="project_level_id" class="layui-select">--><!--<option value="all">全部</option>--><!--</select>--><!--</div>--><!--</div>--><div class="layui-form-item layui-inline"><label class="layui-form-label">属性名称</label><div class="layui-input-inline"><input name="attribute_name" value="<?php echo input('attribute_name'); ?>" class="layui-input"></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><!--<script type="text/javascript">--><!--$.ajax({--><!--url:"<?php echo url('levelArray'); ?>",--><!--method:"GET",--><!--success:function(data){--><!--tree = JSON.parse(data);--><!--var select = "";--><!--var project_level_id = "<?php echo input('project_level_id'); ?>";--><!--for(var i = 0;i < tree.length;i++){--><!--select = "";--><!--if(project_level_id == tree[i].project_level_id){--><!--select = "selected";--><!--}--><!--$("#levelArray").append("<option " + select + " value='" + tree[i].project_level_id + "'><span class=\"color-desc\">" + tree[i].spl + "</span>" + tree[i].title + "</option>");--><!--}--><!--form.render();--><!--}--><!--})--><!--</script>--><table class="layui-table margin-top-10" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-sort-td'><button type="button" data-reload class="layui-btn layui-btn-xs">刷 新</button></th><th class="text-left">属性名称</th><th class='text-left nowrap'>条件</th><th class='text-left nowrap'>用户填写</th><th class='text-left nowrap'>是否必填</th><th class='text-left nowrap'>描述</th><th class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr data-dbclick><td></td><td class="text-left nowrap"><div class="inline-block sub-span-blue"><span><?php echo htmlentities($vo['attribute_name']); ?></span></div></td><td class="text-left nowrap"><div class="inline-block sub-span-blue"><span><?php echo htmlentities($vo['condition_text']); ?></span></div></td><td class="text-left nowrap"><div class="inline-block sub-span-blue"><span><?php echo htmlentities($vo['display_text']); ?></span></div></td><td class="text-left nowrap"><div class="inline-block sub-span-blue"><span><?php echo htmlentities($vo['required_text']); ?></span></div></td><td class="text-left nowrap"><div class="inline-block sub-span-blue"><span><textarea readonly class="layui-text"><?php echo htmlentities($vo['remarks']); ?></textarea></span></div></td><td class='text-left nowrap'><?php if(auth("edit")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑" data-modal='<?php echo url("edit"); ?>?project_attribute_id=<?php echo htmlentities($vo['project_attribute_id']); ?>'>编 辑</a><?php endif; if(auth("remove")): ?><a class="layui-btn layui-btn-danger layui-btn-xs" data-confirm="确定要删除吗?" data-action="<?php echo url('remove'); ?>" data-value="project_attribute_id#<?php echo htmlentities($vo['project_attribute_id']); ?>" data-csrf="<?php echo systoken('remove'); ?>">删 除</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div></div>