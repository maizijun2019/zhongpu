<?php /*a:2:{s:48:"E:\Pro\pro\ZP\app\orders\view\process\index.html";i:1595475123;s:39:"E:\Pro\pro\ZP\app\orders\view\main.html";i:1597306698;}*/ ?>
<div class="layui-card layui-bg-gray"><!-- 

<?php if(auth("add")): ?><button data-modal='<?php echo url("add"); ?>' data-title="添加流程" class='layui-btn layui-btn-sm layui-btn-primary'>添加流程</button><?php endif; ?> --><div class="layui-card-body"><div class="layui-tab layui-tab-card think-bg-white"><div class="layui-tab-content think-box-shadow"><table class="layui-table margin-top-10" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-sort-td'><button type="button" data-reload class="layui-btn layui-btn-xs">刷 新</button></th><th class='text-left nowrap'>简称</th><th class='text-left nowrap'>描述</th><th class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr data-dbclick><td></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><textarea readonly placeholder="什么也没有..." class="layui-text"><?php echo (isset($vo['describe']) && ($vo['describe'] !== '')?$vo['describe']:''); ?></textarea></div></td><td class='text-left nowrap'><?php if(auth("remove")): ?><a class="layui-btn layui-btn-danger layui-btn-xs" data-confirm="确定要删除吗?" data-action="<?php echo url('remove'); ?>" data-value="orders_process_id#<?php echo htmlentities($vo['orders_process_id']); ?>" data-csrf="<?php echo systoken('remove'); ?>">删 除</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div></div>