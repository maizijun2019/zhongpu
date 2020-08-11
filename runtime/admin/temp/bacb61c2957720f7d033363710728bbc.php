<?php /*a:2:{s:45:"E:\Pro\pro\ZP\app\admin\view\login\index.html";i:1595475123;s:45:"E:\Pro\pro\ZP\app\admin\view\index\index.html";i:1596983923;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><title><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); if(!empty($title)): ?> · <?php endif; ?><?php echo sysconf('site_name'); ?></title><meta charset="utf-8"><meta name="renderer" content="webkit"><meta name="format-detection" content="telephone=no"><meta name="apple-mobile-web-app-capable" content="yes"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=0.4"><link rel="shortcut icon" href="/favicon.ico"><link rel="stylesheet" href="/static/plugs/awesome/fonts.css?at=<?php echo date('md'); ?>"><link rel="stylesheet" href="/static/plugs/layui/css/layui.css?at=<?php echo date('md'); ?>"><link rel="stylesheet" href="/static/theme/css/console.css?at=<?php echo date('md'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"><script>if (location.href.indexOf('#') > -1) location.replace(location.href.split('#')[0])</script><link rel="stylesheet" href="/static/theme/css/login.css"><script>window.ROOT_URL = '';</script><script src="/static/plugs/jquery/pace.min.js"></script><script src="/static/echarts.min.js"></script><style type="text/css">        /* 定义keyframe动画，命名为blink */
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
    </style></head><body class="layui-layout-body"><div class="login-container" data-supersized="/static/theme/img/login/bg1.jpg,/static/theme/img/login/bg2.jpg"><div class="header notselect layui-hide-xs"><a href="<?php echo url('@'); ?>" class="title"><?php echo sysconf('app_name'); ?><span class="padding-left-5 font-s10"><?php echo sysconf('app_version'); ?></span></a><?php if(!(empty($devmode) || (($devmode instanceof \think\Collection || $devmode instanceof \think\Paginator ) && $devmode->isEmpty()))): ?><a class="pull-right layui-anim layui-anim-fadein" href='https://gitee.com/zoujingli/ThinkAdmin'><img src='https://gitee.com/zoujingli/ThinkAdmin/widgets/widget_1.svg' alt='Fork me on Gitee'></a><?php endif; ?></div><form data-login-form onsubmit="return false" method="post" class="layui-anim layui-anim-upbit" autocomplete="off"><h2 class="notselect">系统管理</h2><ul><li class="username"><label><i class="layui-icon layui-icon-username"></i><input class="layui-input" required pattern="^\S{4,}$" name="username" autofocus autocomplete="off" placeholder="登录账号" title="请输入登录账号"></label></li><li class="password"><label><i class="layui-icon layui-icon-password"></i><input class="layui-input" required pattern="^\S{4,}$" name="password" maxlength="32" type="password" autocomplete="off" placeholder="登录密码" title="请输入登录密码"></label></li><li class="verify layui-hide"><label class="inline-block relative"><i class="layui-icon layui-icon-picture-fine"></i><input class="layui-input" required pattern="^\S{4,}$" name="verify" maxlength="4" autocomplete="off" placeholder="验证码" title="请输入验证码"></label><label data-captcha="<?php echo url('admin/login/captcha',[],false); ?>" data-field-verify="verify" data-field-uniqid="uniqid" data-captcha-type="<?php echo htmlentities($captcha_type); ?>" data-captcha-token="<?php echo htmlentities($captcha_token); ?>"></label></li><li class="text-center padding-top-20"><button type="submit" class="layui-btn layui-disabled full-width" data-form-loaded="立即登入">正在载入</button></li></ul></form><div class="footer notselect"><p class="layui-hide-xs"><a target="_blank" href="https://www.google.cn/chrome">推荐使用谷歌浏览器</a></p><?php echo sysconf('site_copy'); if(sysconf('beian')): ?><span class="padding-5">|</span><a target="_blank" href="http://beian.miit.gov.cn"><?php echo sysconf('beian'); ?></a><?php endif; if(sysconf('miitbeian')): ?><span class="padding-5">|</span><a target="_blank" href="http://beian.miit.gov.cn"><?php echo sysconf('miitbeian'); ?></a><?php endif; ?></div></div><script src="/static/plugs/layui/layui.all.js"></script><script src="/static/plugs/require/require.js"></script><script src="/static/admin.js"></script><?php if(session('user.username')): ?><script>    // $.ajax({
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
</script><?php endif; ?><script src="/static/login.js"></script><script src="/static/plugs/supersized/supersized.3.2.7.min.js"></script></body></html>