<table id="campaignDraftListsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.campaignLists}',
    method:'post',
    toolbar:'#campaignDraftListsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:'',
    onLoadSuccess:function(row, data){
        //campaignDraftListsModule.expandAll();
    },
    view: detailview,
    detailFormatter:function(index,row){
        return campaignDraftListsModule.detailFormatter();
    },
    onExpandRow:function(index,row){
        var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
        var href = '{$urlHrefs.campaignListsDetail}';
        var campaignId = row.campaign_id;
        href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
        ddv.panel({
            height:100,
            border:false,
            cache:false,
            href:href,
            onLoad:function(){
                $('#campaignDraftListsDatagrid').datagrid('fixDetailRowHeight',index);
            }
        });
        $('#campaignDraftListsDatagrid').datagrid('fixDetailRowHeight',index);
    }
    ">
    <thead>
    <tr>
        <th data-options="field:'operate',width:150,align:'center',formatter:campaignDraftListsModule.operate">操作</th>
        <th data-options="field:'subject',width:200,align:'center'">邮件主题</th>
        <th data-options="field:'entered',width:150,align:'center'">创建时间</th>
        <th data-options="field:'status',width:100,align:'center',formatter:campaignListsModule.formatCampaignStatus">当前状态</th>
        <th data-options="field:'age',width:150,align:'center',formatter:campaignDraftListsModule.formatAge">存在时间</th>
    </tr>
    </thead>
</table>
<div id="campaignDraftListsToolbar" class="p-1">
    <form id="campaignDraftListsToolbarForm">
        主题: <input name="search[subject]" class="easyui-textbox"
                        data-options="width:200" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                    onClick:function(){ campaignDraftListsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                    onClick:function(){ campaignDraftListsModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var campaignDraftListsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#campaignDraftListsDatagrid',
        searchform:'#campaignDraftListsToolbarForm',
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="campaignDraftListsModule.delete(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="删除"><i class="fa fa-trash fa-lg">删除</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="campaignDraftListsModule.edit(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="编辑"><i class="fa fa-pencil-square fa-lg">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius my-1" onclick="campaignListsModule.view(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="查看"><i class="fa fa-eye fa-lg">查看</i></a>');
            return btns.join(' ');
        },
        formatAge:function(val, row){
            return '<span class="badge badge-default">' + val + '</span>';
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        detailFormatter:function(){
            return '<div class="ddv" style="overflow: auto;"></div>';
        },
        expandAll:function(){
            var that = this;
            var rows = $(that.datagrid).datagrid('getRows');
            for(var i= 0,count=rows.length; i<count; i++){
                var row = rows[i];
                var index = $(that.datagrid).datagrid('getRowIndex', row);
                $(that.datagrid).datagrid('expandRow', index);
            }
        },
        search:function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            //reset the query parameter
            $.each($(that.searchform).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $.each($(that.searchform).serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            $(that.datagrid).datagrid('load');
        },
        reset:function(){
            var that = this;
            $(that.searchform).form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($(that.searchform).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $(that.datagrid).datagrid('load');
        },
        delete:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.sendCampaignDelete}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $.messager.confirm('提示', `确认删除投递活动"${subject}"吗?`, function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {}, function(res){
                    $.messager.progress('close');
                    if(!res.code){
                        $.app.method.alertError(null, res.msg);
                    }else{
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                    }
                }, 'json');
            });
        },
        edit:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.sendCampaignEdit}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $(that.dialog).dialog({
                title: '修改投递活动 - ' + subject,
                iconCls: 'fa fa-pencil-square',
                width: '90%',
                height: '100%',
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[],
                onClose:function(){
                    that.reload();
                }
            });
            $(that.dialog).dialog('center');
        }
    };

</script>