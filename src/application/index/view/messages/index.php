<?php
use app\index\logic\Messages as MessagesLogic;
?>
<table id="messagesDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    checkOnSelect:false,
    selectOnCheck:false,
    url:'{$urlHrefs.index}',
    method:'post',
    toolbar:'#messagesToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:true,
    title:'',
    onDblClickRow:function(index, row){
        //messagesModule.view(row.message_id);
    },
    view: detailview,
    detailFormatter:function(index,row){
        return messagesModule.detailFormatter();
    },
    onExpandRow:function(index,row){
        var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
        var href = '{$urlHrefs.content}';
        var messageId = row.message_id;
        href = GLOBAL.func.addUrlParam(href, 'messageId', messageId);
        ddv.panel({
            height:100,
            border:false,
            cache:false,
            href:href,
            onLoad:function(){
                $('#messagesDatagrid').datagrid('fixDetailRowHeight',index);
                messagesModule.markRead(index, messageId);
            }
        });
        $('#messagesDatagrid').datagrid('fixDetailRowHeight',index);
    }
    ">
    <thead>
    <tr>
        <th data-options="field:'ck',checkbox:true"></th>
        <th data-options="field:'entered',width:300,align:'center'">时间</th>
        <th data-options="field:'title',width:600,align:'center',formatter:messagesModule.formatTitle">标题</th>
        <th data-options="field:'category',width:200,align:'center',sortable:true,formatter:messagesModule.formatCategory">类别</th>
        <th data-options="field:'is_read',width:100,align:'center',formatter:messagesModule.formatRead">状态</th>
    </tr>
    </thead>
</table>
<div id="messagesToolbar">
    <div class="datagrid-toolbar-buttons">
        <a href="#" class="easyui-linkbutton" data-options="plain:true,
            onClick:function(){ messagesModule.markSelectedRead(); },iconCls:'fa fa-check-square-o'">所选已读</a>
        <a href="#" class="easyui-linkbutton" data-options="plain:true,
            onClick:function(){ messagesModule.markAllRead(); },iconCls:'fa fa-clone'">全部已读</a>
    </div>
</div>
<script>
    var messagesModule = {
        dialog: '#globel-dialog-div',
        dialog2: '#globel-dialog2-div',
        datagrid: '#messagesDatagrid',
        formatTitle:function(val, row){
            if(row.is_read == 0){
                return '<strong>' + val + '</strong>';
            }else{
                return val;
            }
        },
        formatCategory: function (val, row) {
            return <?=json_encode(MessagesLogic::$messageCategoryHtmlDefs)?>[val];
        },
        formatRead:function(val, row){
            if(val == 0){
                return '<span class="badge badge-secondary">未读</span>';
            }else{
                return '<span class="badge badge-success">已读</span>';
            }
        },
        detailFormatter:function(){
            return '<div class="ddv" style="overflow: auto"></div>';
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        load: function(){
            $(this.datagrid).datagrid('load');
        },
        search:function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            //reset the query parameter
            $.each($("#messagesToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $.each($("#messagesToolbarForm").serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            that.load();
        },
        reset: function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($("#messagesToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            that.load();
        },
        markRead:function(index, messageId){
            var that = this;
            var href = '<?=url('index/Messages/markRead')?>';
            //$.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {messageId:messageId}, function(res){
                //$.messager.progress('close');
                /*
                $(that.datagrid).datagrid('updateRow', {
                        index: index,
                        row: {
                            is_read: 1
                        }
                    }
                );
                $(that.datagrid).datagrid('refreshRow', index);
                */
                //that.reload();
            }, 'json');
        },
        markSelectedRead:function(){
            var messageIds = [];
            var rows = $(messagesModule.datagrid).datagrid('getChecked');
            for(var i= 0; i<rows.length; i++){
                messageIds.push(rows[i].message_id);
            }
            if(messageIds.length == 0){
                return;
            }
            var that = this;
            var href = '<?=url('index/Messages/markSelectedRead')?>';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {
                messageIds:messageIds
            }, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.tip('提示', res.msg, 'info');
                    that.load();
                }
            }, 'json');
        },
        markAllRead: function(){
            var that = this;
            var href = '<?=url('index/Messages/markAllRead')?>';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {}, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.tip('提示', res.msg, 'info');
                    that.load();
                }
            }, 'json');
        },
        view: function(messageId){}
    };
</script>