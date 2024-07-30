<div style="width:100%;height:30px;text-align:center;margin-top:5px">
    <input id="dateBeginInput" class="easyui-datebox" data-options="onChange: trendChartModule.onDateChanged" value="<?=$beginDate?>"/>
    -
    <input id="dateEndInput" class="easyui-datebox" data-options="onChange: trendChartModule.onDateChanged" value="<?=$endDate?>"/>
</div>
<div id="trendChart" style="width:100%;height:85%">
    <div id="trendChartContainer" style="width:100%;height:100%"></div>
</div>
<script type="text/javascript">
    var trendChartModule = {
        width:300,
        height:600,
        chart: null,
        init: function(){
            trendChartModule.reLoadChart();
        },
        onDateChanged: function(newValue, oldValue){
            trendChartModule.reLoadChart();
        },
        reLoadChart: function(){
            var beginDate = $('#dateBeginInput').datebox('getValue');
            var endDate = $('#dateEndInput').datebox('getValue');
            $.post('<?=url('index/Dashboard/trend')?>', {beginDate:beginDate, endDate:endDate}, function(res){
                trendChartModule.chart = $('#trendChartContainer').highcharts({
                    chart: {
                        type: 'line',
                        margin: [ 50, 50, 120, 80]
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: 'camellist投递邮件数据趋势图'
                    },
                    xAxis: {
                        labels: {
                            rotation: - 45,
                            align: 'right',
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        },
                        categories: res.dates
                    },
                    yAxis: {
                        title: {
                            text: '人数'
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name} <b>{point.y:,.0f}</b> 人数'
                    },
                    series: [{
                        name: '投递邮件数量',
                        color: '#EE9A49',
                        data: res.sentSubscribers,
                        dataLabels: {
                                enabled: true,
                                rotation: - 90,
                                color: '#FFFFFF',
                                align: 'right',
                                x: 4,
                                y: 10,
                                style: {
                                    fontSize: '13px',
                                    fontFamily: 'Verdana, sans-serif',
                                    textShadow: '0 0 3px black'
                                }
                            }
                        }
                    ]
                });
                trendChartModule.resizeChart();
            });
        },
        resizeChart:function(){
            if(trendChartModule.chart) {
                var w1 = trendChartModule.width;
                var h1 = trendChartModule.height;
                if(w1 && h1 && w1>100) {
                    var chart = $('#trendChartContainer').highcharts();
                    chart.setSize(w1, h1, false);
                    chart.reflow();
                }
            }
        }
    };
    $(window).off('resize.trendChart').on('resize.trendChart',function() {
        setTimeout(function() {
            var w1 = $("#trendChart").width();
            var h1 = $("#trendChart").height();
            trendChartModule.width = w1;
            trendChartModule.height = h1;
            trendChartModule.resizeChart();
        },200);
    });
    $.parser.onComplete = function(){
        var w1 = $("#trendChart").width();
        var h1 = $("#trendChart").height();
        trendChartModule.width = w1;
        trendChartModule.height = h1;

        trendChartModule.init();
        $.parser.onComplete = $.noop;
    };
</script>