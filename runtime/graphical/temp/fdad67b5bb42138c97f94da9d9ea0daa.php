<?php /*a:2:{s:49:"E:\Pro\pro\ZP\app\graphical\view\index\index.html";i:1595475123;s:42:"E:\Pro\pro\ZP\app\graphical\view\main.html";i:1595475123;}*/ ?>
<div class="layui-card layui-bg-gray"><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body"><div class="think-box-shadow"><table class="layui-table" lay-skin="line"><thead></thead><tbody><div id="main" style="width: 600px;height:400px;"></div><script type="text/javascript">                $.ajax({
                    url:"<?php echo url('api/query/ordersGraphical'); ?>",
                    method:"GET",
                    success:function(data){
                        data = JSON.parse(data);
                        // 基于准备好的dom，初始化echarts实例
                        var myChart = echarts.init(document.getElementById('main'));

                        // 指定图表的配置项和数据
                        var option = {
                            title: {
                                text: data["title"]
                            },
                            tooltip: {},
                            legend: {
                                data: data["legend"]
                            },
                            xAxis: {
                                data: data["xAxis"]
                            },
                            yAxis: {
                                data: data["yAxis"]
                            },
                            series: data["series"]
                            // series: [{
                            //     name: '项目',
                            //     type: 'bar',
                            //     data: [5, 20, 36, 10, 10, 20]
                            // },{
                            //     name: '两化',
                            //     type: 'bar',
                            //     data: [1,2,3,4,5,6]
                            // },{
                            //     name: '知识产权',
                            //     type: 'bar',
                            //     data: [1,2,3,4,5,6]
                            // },{
                            //     name: '财务',
                            //     type: 'bar',
                            //     data: [1,2,3,4,5,6]
                            // }]
                        };

                        // 使用刚指定的配置项和数据显示图表。
                        myChart.setOption(option);
                    },
                    error:function(){
                        layer.msg("网络错误,请检查网络");
                    }
                });
            </script></tbody></table></div></div></div>