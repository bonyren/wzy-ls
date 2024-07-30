<table id="auditLogsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    selectOnCheck:false,
    checkOnSelect:false,
    url:'{$urlHrefs.index}',
    method:'post',
    toolbar:'#auditLogsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:''">
    <thead>
    <tr>
        <th data-options="field:'type',width:80,align:'center',formatter:auditLogsModule.formatType">类型</th>
        <th data-options="field:'entered',width:150,fixed:true,align:'center'">时间</th>
        <th data-options="field:'desc',width:600,align:'left'">描述</th>
        <th data-options="field:'realname',width:100,align:'center'">操作人</th>
        <th data-options="field:'device',width:60,align:'center',formatter:auditLogsModule.formatDevice">设备</th>
        <th data-options="field:'ip',width:100,align:'center'">Ip</th>
    </tr>
    </thead>
</table>
<div id="auditLogsToolbar" class="p-1">
    <form id="auditLogsToolbarForm">
        关键字: <input name="search[keyword]" class="easyui-textbox" data-options="width:200" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                        onClick:function(){ auditLogsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                        onClick:function(){ auditLogsModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var auditLogsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#auditLogsDatagrid',
        searchForm:'#auditLogsToolbarForm',
        formatType:function(val, row){
            var typeObj = <?=json_encode(\app\index\model\Base::$auditLogTypeHtmlDefs)?>;
            return typeObj[val];
        },
        formatDevice:function(val, row){
            var deviceObj = <?=json_encode(\app\index\model\Base::$auditLogDeviceHtmlDefs)?>;
            return deviceObj[val];
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        reset:function(){
            var that = this;
            $(that.searchForm).form('reset');
            $(that.datagrid).datagrid('load', {});
        },
        search:function(){
            var that = this;
            var paramObj = {};
            //reset the query parameter
            $.each($(that.searchForm).serializeArray(), function() {
                paramObj[this['name']] = this['value'];
            });
            $(that.datagrid).datagrid('load', paramObj);
        }
    };

</script>