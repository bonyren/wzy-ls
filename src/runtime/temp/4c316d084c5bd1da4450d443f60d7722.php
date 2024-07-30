<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"D:\MY-workspace\wzyer-list\output\web\public\..\application\index\view\index\main.php";i:1721966870;}*/ ?>
<div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'north',
        collapsible:true,
        border:false,
        iconCls:'fa fa-pie-chart',
        title:'',
        collapsed:false,
        href:'<?=$urlHrefs['statistic']?>'" style="height: 30%;">
    </div>
    <div data-options="region:'center',border:false">
        <div class="easyui-tabs" data-options="fit:true,border:false">
            <div data-options="title:'数据统计',iconCls:'fa fa-line-chart',href:'<?=$urlHrefs['trend']?>'"></div>
            <div data-options="title:'最新事件',iconCls:'fa fa-clock-o',href:'<?=$urlHrefs['events']?>'"></div>
        </div>
    </div>
</div>