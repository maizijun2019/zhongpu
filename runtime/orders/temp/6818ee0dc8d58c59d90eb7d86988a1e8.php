<?php /*a:2:{s:57:"E:\Pro\pro\ZP\app\orders\view\index\responsible_info.html";i:1595475123;s:39:"E:\Pro\pro\ZP\app\orders\view\main.html";i:1595475123;}*/ ?>
<div class="layui-card layui-bg-gray"><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body"><div class="layui-tab layui-tab-card think-bg-white"><div class="layui-tab-content think-box-shadow"><table class="layui-table margin-top-10" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-sort-td'><button type="button" data-reload class="layui-btn layui-btn-xs">刷 新</button></th><th class='text-left nowrap'></th><th class='text-left nowrap'>用户名</th><th class='text-left nowrap'>昵称</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr data-dbclick><td></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><img alt="img" width="40px" height="40px" class="text-top margin-right-10" data-tips-image src="<?php echo htmlentities((isset($vo['headimg']) && ($vo['headimg'] !== '')?$vo['headimg']:'')); ?>"></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['username']) && ($vo['username'] !== '')?$vo['username']:''); ?></span></div></td><td class='text-left nowrap'><div class="inline-block sub-span-blue"><span><?php echo (isset($vo['nickname']) && ($vo['nickname'] !== '')?$vo['nickname']:''); ?></span></div></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div></div>