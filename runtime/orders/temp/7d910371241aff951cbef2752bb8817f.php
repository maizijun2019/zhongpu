<?php /*a:3:{s:45:"E:\Pro\pro\ZP\app\orders\view\auth\index.html";i:1595475123;s:39:"E:\Pro\pro\ZP\app\orders\view\main.html";i:1597306698;s:52:"E:\Pro\pro\ZP\app\orders\view\auth\index_search.html";i:1595475123;}*/ ?>
<div class="layui-card layui-bg-gray"><!-- 

<?php if(auth("add")): ?><button data-modal='<?php echo url("add"); ?>' data-title="添加权限组" class='layui-btn layui-btn-sm layui-btn-primary'>添加权限组</button><?php endif; ?> --><div class="layui-card-body"><div class="layui-tab layui-tab-card think-bg-white"><div class="layui-tab-content think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">分组名称</label><div class="layui-input-inline"><input name="auth_name" value="<?php echo input('auth_name'); ?>" placeholder="请输入分组名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><table class="layui-table margin-top-10" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-sort-td'><button type="button" data-reload class="layui-btn layui-btn-xs">刷 新</button></th><th class='text-left nowrap'>权限名称</th><th class='text-left nowrap'>人数</th><th class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr data-dbclick><td></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['auth_name']) && ($vo['auth_name'] !== '')?$vo['auth_name']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['user_size']) && ($vo['user_size'] !== '')?$vo['user_size']:0); ?></span></div></td><td class='text-left nowrap'><?php if(auth("edit")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑" data-modal='<?php echo url("edit"); ?>?orders_auth_id=<?php echo htmlentities($vo['orders_auth_id']); ?>'>编 辑</a><?php endif; if(auth("remove")): ?><a class="layui-btn layui-btn-danger layui-btn-xs" data-confirm="确定要删除吗?" data-action="<?php echo url('remove'); ?>" data-value="orders_auth_id#<?php echo htmlentities($vo['orders_auth_id']); ?>" data-csrf="<?php echo systoken('remove'); ?>">删 除</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div></div>