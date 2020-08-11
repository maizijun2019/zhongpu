<?php /*a:1:{s:45:"E:\Pro\pro\ZP\app\admin\view\index\index.html";i:1596983923;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><title><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); if(!empty($title)): ?> · <?php endif; ?><?php echo sysconf('site_name'); ?></title><meta charset="utf-8"><meta name="renderer" content="webkit"><meta name="format-detection" content="telephone=no"><meta name="apple-mobile-web-app-capable" content="yes"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=0.4"><link rel="shortcut icon" href="/favicon.ico"><link rel="stylesheet" href="/static/plugs/awesome/fonts.css?at=<?php echo date('md'); ?>"><link rel="stylesheet" href="/static/plugs/layui/css/layui.css?at=<?php echo date('md'); ?>"><link rel="stylesheet" href="/static/theme/css/console.css?at=<?php echo date('md'); ?>"><script>window.ROOT_URL = '';</script><script src="/static/plugs/jquery/pace.min.js"></script><script src="/static/echarts.min.js"></script><style type="text/css">        /* 定义keyframe动画，命名为blink */
        @keyframes blink{
            0%{opacity: 1;}
            100%{opacity: 0;}
        }
        /* 添加兼容性前缀 */
        @-webkit-keyframes blink {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
        @-moz-keyframes blink {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
        @-ms-keyframes blink {
            0% {opacity: 1; }
            100% { opacity: 0;}
        }
        @-o-keyframes blink {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
        /* 定义blink类*/
        .blink{
            color: #dd4814;
            animation: blink 1s linear infinite;
            /* 其它浏览器兼容性前缀 */
            -webkit-animation: blink 1s linear infinite;
            -moz-animation: blink 1s linear infinite;
            -ms-animation: blink 1s linear infinite;
            -o-animation: blink 1s linear infinite;
        }
    </style></head><body class="layui-layout-body"><div class="layui-layout layui-layout-admin layui-layout-left-hide"><!-- 顶部菜单 开始 --><div class="layui-header notselect"><a href="<?php echo url('@'); ?>" class="layui-logo layui-elip"><?php echo sysconf('app_name'); if(sysconf('app_version')): ?><sup class="padding-left-5"><?php echo sysconf('app_version'); ?></sup><?php endif; ?></a><ul class="layui-nav layui-layout-left"><li class="layui-nav-item" lay-unselect><a class="text-center" data-target-menu-type><i class="layui-icon layui-icon-spread-left"></i></a></li><?php foreach($menus as $one): ?><li class="layui-nav-item"><a data-menu-node="m-<?php echo htmlentities($one['id']); ?>" data-open="<?php echo htmlentities($one['url']); ?>"><?php if(!(empty($one['icon']) || (($one['icon'] instanceof \think\Collection || $one['icon'] instanceof \think\Paginator ) && $one['icon']->isEmpty()))): ?><span class='<?php echo htmlentities($one['icon']); ?> padding-right-5'></span><?php endif; ?><span><?php echo htmlentities((isset($one['title']) && ($one['title'] !== '')?$one['title']:'')); ?></span></a></li><?php endforeach; ?></ul><ul class="layui-nav layui-layout-right"><li lay-unselect class="layui-nav-item"><a data-reload><i class="layui-icon layui-icon-refresh-3"></i></a></li><?php if(session('user.username')): ?><li class="layui-nav-item"><dl class="layui-nav-child"><dd lay-unselect><a data-modal="<?php echo url('message/index/listArray'); ?>" id="message"><i class="layui-icon layui-icon-email margin-right-5"></i> 未读消息<span id="message_span" style="color:red;"></span></a></dd><dd lay-unselect><a data-modal="<?php echo url('admin/index/info',['id'=>session('user.id')]); ?>"><i class="layui-icon layui-icon-set-fill margin-right-5"></i> 基本资料</a></dd><dd lay-unselect><a data-modal="<?php echo url('admin/index/pass',['id'=>session('user.id')]); ?>"><i class="layui-icon layui-icon-component margin-right-5"></i> 安全设置</a></dd><dd lay-unselect><a data-modal="<?php echo url('admin/index/optimize'); ?>"><i class="layui-icon layui-icon-template-1 margin-right-5"></i>缓存加速</a></dd><dd lay-unselect><a data-modal="<?php echo url('admin/index/clear'); ?>"><i class="layui-icon layui-icon-fonts-clear margin-right-5"></i>清理缓存</a></dd><dd lay-unselect><a data-load="<?php echo url('admin/login/out'); ?>" data-confirm="确定要退出登录吗？"><i class="layui-icon layui-icon-release margin-right-5"></i> 退出登录</a></dd></dl><a><i class="layui-icon layui-icon-username margin-right-5"></i><span id="blink"><?php echo session('user.username'); ?></span></a></li><?php else: ?><li class="layui-nav-item"><a data-href="<?php echo url('@admin/login'); ?>"><i class="layui-icon layui-icon-username"></i> 立即登录</a></li><?php endif; ?></ul></div><!-- 顶部菜单 结束 --><!-- 左则菜单 开始 --><div class="layui-side layui-bg-black notselect"><div class="layui-side-scroll"><?php foreach($menus as $one): if(!(empty($one['sub']) || (($one['sub'] instanceof \think\Collection || $one['sub'] instanceof \think\Paginator ) && $one['sub']->isEmpty()))): ?><ul class="layui-nav layui-nav-tree layui-hide" data-menu-layout="m-<?php echo htmlentities($one['id']); ?>"><?php foreach($one['sub'] as $two): if(empty($two['sub']) || (($two['sub'] instanceof \think\Collection || $two['sub'] instanceof \think\Paginator ) && $two['sub']->isEmpty())): ?><li class="layui-nav-item"><a data-target-tips="<?php echo htmlentities($two['title']); ?>" data-menu-node="m-<?php echo htmlentities($one['id']); ?>-<?php echo htmlentities($two['id']); ?>" data-open="<?php echo htmlentities($two['url']); ?>"><span class='<?php echo htmlentities((isset($two['icon']) && ($two['icon'] !== '')?$two['icon']:"layui-icon layui-icon-link")); ?>'></span><span class="nav-text padding-left-5"><?php echo htmlentities($two['title']); ?></span></a></li><?php else: ?><li class="layui-nav-item" data-submenu-layout='m-<?php echo htmlentities($one['id']); ?>-<?php echo htmlentities($two['id']); ?>'><a data-target-tips="<?php echo htmlentities($two['title']); ?>" style="background:#393D49"><span class='nav-icon layui-hide <?php echo htmlentities((isset($two['icon']) && ($two['icon'] !== '')?$two['icon']:"layui-icon layui-icon-triangle-d")); ?>'></span><span class="nav-text padding-left-5"><?php echo htmlentities($two['title']); ?></span></a><dl class="layui-nav-child"><?php foreach($two['sub'] as $thr): ?><dd><a data-target-tips="<?php echo htmlentities($thr['title']); ?>" data-open="<?php echo htmlentities($thr['url']); ?>" data-menu-node="m-<?php echo htmlentities($one['id']); ?>-<?php echo htmlentities($two['id']); ?>-<?php echo htmlentities($thr['id']); ?>"><span class='nav-icon padding-left-5 <?php echo htmlentities((isset($thr['icon']) && ($thr['icon'] !== '')?$thr['icon']:"layui-icon layui-icon-link")); ?>'></span><span class="nav-text padding-left-5"><?php echo htmlentities($thr['title']); ?></span></a></dd><?php endforeach; ?></dl></li><?php endif; ?><?php endforeach; ?></ul><?php endif; ?><?php endforeach; ?></div></div><!-- 左则菜单 结束 --><!-- 主体内容 开始 --><div class="layui-body layui-bg-gray"></div><!-- 主体内容 结束 --></div><script src="/static/plugs/layui/layui.all.js"></script><script src="/static/plugs/require/require.js"></script><script src="/static/admin.js"></script><?php if(session('user.username')): ?><script>    // $.ajax({
    //     url:"<?php echo url('api/msg/queryRemind'); ?>",
    //     success:function(result){
    //         result = JSON.parse(result);
    //         if(result["remind"]){
    //             layer.msg('有新的项目等待处理', {
    //                 time: 0 //不自动关闭
    //                 ,btn: ['确定', '今日不再提醒']
    //                 ,btn2: function(index){
    //                     $.ajax({
    //                         url:"<?php echo url('api/msg/todayNoReminders'); ?>"
    //                     });
    //                     layer.close(index);
    //                     layer.msg('已为您设置今日不再提醒', {
    //                         icon: 6
    //                         ,btn: ['OK']
    //                     });
    //                 }
    //             });
    //         }
    //     }
    // });

    showUnreadNews();

    function showUnreadNews()
    {
        $.ajax({
            url:"<?php echo url('message/index/messageCount'); ?>",
            success:function(count){
                if(count > 0){
                    $("#message_span").html("(" + count + ")");
                    $("#blink").addClass("blink");
                }else{
                    $("#message_span").html("");
                    $("#blink").removeClass("blink");
                }
            }
        });
    }
    setInterval('showUnreadNews()',3000);//轮询执行，3000ms一次
</script><?php endif; ?></body></html>