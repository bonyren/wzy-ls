<?php
use app\index\service\EventLogs;
?>
<table id="eventsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.events}',
    method:'post',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:true
    ">
    <thead>
        <tr>
            <th data-options="field:'event_log_id',width:100,fixed:true,align:'center'">ID</th>
            <th data-options="field:'entered',width:200,fixed:true,align:'center'">事件</th>
            <th data-options="field:'severity',width:100,fixed:true,align:'center',formatter:eventsModule.formatSeverity">级别</th>
            <th data-options="field:'entry',width:100,align:'left'">内容</th>
        </tr>
    </thead>
</table>
<script>
    var eventsModule = {
        formatSeverity:function(val){
            if(val == <?=EventLogs::eSeverityError?>){
                return '<span class="label label-danger radius my-1"><?=EventLogs::$eSeverityDefs[EventLogs::eSeverityError]?></span>';
            }else if(val == <?=EventLogs::eSeverityWarning?>){
                return '<span class="label label-warning radius my-1"><?=EventLogs::$eSeverityDefs[EventLogs::eSeverityWarning]?></span>';
            }else{
                return '<span class="label label-secondary radius my-1"><?=EventLogs::$eSeverityDefs[EventLogs::eSeverityInfo]?></span>';
            }
        }
    };
</script>