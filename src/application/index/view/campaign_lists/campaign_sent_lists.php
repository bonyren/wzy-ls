<table id="campaignSentListsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.campaignLists}',
    method:'post',
    toolbar:'#campaignSentListsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:'',
    view: detailview,
    onLoadSuccess:function(row, data){
        campaignSentListsModule.expandAll();
    },
    detailFormatter:function(index,row){
        return campaignSentListsModule.detailFormatter();
    },
    onExpandRow:function(index,row){
        var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
        var href = '{$urlHrefs.campaignListsDetail}';
        var campaignId = row.campaign_id;
        href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
        ddv.panel({
            height:120,
            border:false,
            cache:false,
            href:href,
            onLoad:function(){
                $('#campaignSentListsDatagrid').datagrid('fixDetailRowHeight',index);
            }
        });
        $('#campaignSentListsDatagrid').datagrid('fixDetailRowHeight',index);
    }">
    <thead>
    <tr>
        <th data-options="field:'operate',width:150,align:'center',formatter:campaignSentListsModule.operate">操作</th>
        <th data-options="field:'subject',width:200,align:'center'">主题</th>
        <th data-options="field:'entered',width:150,align:'center'">创建</th>
        <th data-options="field:'status',width:100,align:'center',formatter:campaignListsModule.formatCampaignStatus">状态</th>
    </tr>
    </thead>
</table>
<div id="campaignSentListsToolbar" class="p-1">
    <form id="campaignSentListsToolbarForm">
        主题: <input name="search[subject]" class="easyui-textbox"
                        data-options="width:200" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                    onClick:function(){ campaignSentListsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                    onClick:function(){ campaignSentListsModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var campaignSentListsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#campaignSentListsDatagrid',
        searchform:'#campaignSentListsToolbarForm',
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-info size-MINI radius" onclick="campaignSentListsModule.requeue(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="重新投递"><i class="fa fa-play fa-lg">重新投递</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius" onclick="campaignListsModule.view(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="查看"><i class="fa fa-eye fa-lg">查看</i></a>');
            return btns.join(' ');
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
        requeue:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignRequeue}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $.messager.confirm('提示', `确认重新开始投递活动"${subject}"吗?`, function(result){
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
        }
    };

</script>