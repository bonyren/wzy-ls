<?php
use app\index\Defs as IndexDefs;
?>
<table id="listsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.lists}',
    method:'post',
    toolbar:'#listsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    rowStyler:listsModule.rowStyler
    ">
    <thead>
    <tr>
        <th data-options="field:'operate',width:100,align:'center',formatter:listsModule.operate">操作</th>
        <th data-options="field:'name',width:200,align:'center',sortable:true">集合名称</th>
        <th data-options="field:'formatMembers',width:150,align:'center',formatter:listsModule.formatMembers">订阅者数量</th>
        <th data-options="field:'modified',width:130,align:'center'">修改时间</th>
        <th data-options="field:'active',width:60,align:'center',formatter:listsModule.formatActive">状态</th>
    </tr>
    </thead>
</table>
<div id="listsToolbar" class="p-1">
    <form id="ListsSearchToolbarForm" class="datagrid-toolbar-search-form">
        集合名称:
        <input name="search[name]" class="easyui-textbox" data-options="width:150" />
        状态:
        <select name="search[active]" class="easyui-combobox" editable="false" panelHeight="auto" style="width:100px">
            <option value="0" selected>全部</option>
            <?php foreach(IndexDefs::$eListActiveDefs as $key=>$value){ ?>
                <option value="<?=$key?>" ><?=$value?></option>
            <?php } ?>
        </select>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
            onClick:function(){ listsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
            onClick:function(){ listsModule.reset(); }">重置
        </a>
    </form>
    <div class="line my-1"></div>
    <div>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ listsModule.add(); },iconCls:'fa fa-plus'">新增集合</a>
    </div>
</div>
<script>
    var listsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#listsDatagrid',
        rowStyler:function(index, row){
            if(row.active == <?=IndexDefs::eListInactive?>){
                return DG_ROW_CSS.rowDel;
            }
        },
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="listsModule.edit(' + row.list_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="编辑"><i class="fa fa-pencil-square-o">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="listsModule.delete(' + row.list_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="删除"><i class="fa fa-trash-o">删除</i></a>');
            return btns.join(' ');
        },
        formatActive:function(val, row){
            if(row.active == <?=IndexDefs::eListActive?>){
                return '<a href="javascript:;" onclick="listsModule.active(' + row.list_id + ', false, ' + '\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="失效该集合"><i class="fa fa-check-square-o fa-lg"></i></a>';
            }else{
                return '<a href="javascript:;" onclick="listsModule.active(' + row.list_id + ', true, ' + '\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="激活该集合"><i class="fa fa-square-o fa-lg"></i></a>';
            }
        },
        formatMembers:function(val, row){
            return  '<a href="javascript:;" onclick="listsModule.viewMembers(' + row.list_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="查看该集合的订阅者"><i class="fa fa-eye"></i></a>|' +
                    '<span class="text-success font-weight-bold" title="订阅者数量">' +  row.subscriber_count + '</span>' +
                    ' <span class="text-muted font-weight-bold text-deleted" title="黑名单数量">' +  row.subscriber_blacklisted_count + '</span>' +
                    '|<a href="javascript:;" onclick="listsModule.importMembers(' + row.list_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.name) + '\')" title="导入订阅者"><i class="fa fa-user-plus"></i></a>';
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        search:function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            //reset the query parameter
            $.each($("#ListsSearchToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $.each($("#ListsSearchToolbarForm").serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            $(that.datagrid).datagrid('load');
        },
        reset:function(){
            var that = this;
            $("#ListsSearchToolbarForm").form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($("#ListsSearchToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $(that.datagrid).datagrid('load');
        },
        active:function(listId, isActive, title){
            var that = this;
            var href = '{$urlHrefs.updateActive}';
            href = GLOBAL.func.addUrlParam(href, 'listId', listId);
            var confirmStr = `确认激活"${title}"集合吗?`;
            if(!isActive){
                confirmStr = `确认失效"${title}"集合吗?`;
            }
            $.messager.confirm('提示', confirmStr, function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {isActive:isActive?1:0}, function(res){
                    $.messager.progress('close');
                    if(!res.code){
                        $.app.method.alertError(null, res.msg);
                    }else{
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                        listsTreeModule.reload();
                    }
                }, 'json');
            });
        },
        add:function(){
            var that = this;
            var href = '{$urlHrefs.add}';
            $(that.dialog).dialog({
                title: '新增集合',
                iconCls: 'fa fa-plus-circle',
                width: <?=$loginMobile?"'90%'":450?>,
                height: 300,
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[{
                    text:'确定',
                    iconCls:iconClsDefs.ok,
                    handler: function(){
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function(){
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;
                                $.messager.progress({text:'处理中，请稍候...'});
                                $.post(href, $(this).serialize(), function(res){
                                    $.messager.progress('close');
                                    if(!res.code){
                                        $.app.method.alertError(null, res.msg);
                                    }else{
                                        $.app.method.tip('提示', res.msg, 'info');
                                        $(that.dialog).dialog('close');
                                        that.reload();
                                        listsTreeModule.reload();
                                    }
                                }, 'json');
                                return false;
                            }
                        });
                    }
                },{
                    text:'取消',
                    iconCls:iconClsDefs.cancel,
                    handler: function(){
                        $(that.dialog).dialog('close');
                    }
                }]
            });
            $(that.dialog).dialog('center');
        },
        edit:function(listId, title){
            var that = this;
            var href = '{$urlHrefs.edit}';
            href = GLOBAL.func.addUrlParam(href, 'listId', listId);
            $(that.dialog).dialog({
                title: '修改集合 - ' + title,
                iconCls: 'fa fa-pencil-square',
                width: <?=$loginMobile?"'90%'":450?>,
                height: 300,
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[{
                    text:'确定',
                    iconCls:iconClsDefs.ok,
                    handler: function(){
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function(){
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;
                                $.messager.progress({text:'处理中，请稍候...'});
                                $.post(href, $(this).serialize(), function(res){
                                    $.messager.progress('close');
                                    if(!res.code){
                                        $.app.method.alertError(null, res.msg);
                                    }else{
                                        $.app.method.tip('提示', res.msg, 'info');
                                        $(that.dialog).dialog('close');
                                        that.reload();
                                        listsTreeModule.reload();
                                    }
                                }, 'json');
                                return false;
                            }
                        });
                    }
                },{
                    text:'取消',
                    iconCls:iconClsDefs.cancel,
                    handler: function(){
                        $(that.dialog).dialog('close');
                    }
                }]
            });
            $(that.dialog).dialog('center');
        },
        delete:function(listId, title){
            var that = this;
            var href = '{$urlHrefs.delete}';
            href = GLOBAL.func.addUrlParam(href, 'listId', listId);
            $.messager.confirm('提示', `确认删除"${title}"集合吗?`, function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {}, function(res){
                    $.messager.progress('close');
                    if(!res.code){
                        $.app.method.alertError(null, res.msg);
                    }else{
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                        listsTreeModule.reload();
                    }
                }, 'json');
            });
        },
        viewMembers: function(listId, name){
            listsTreeModule.selectList(listId);
        },
        importMembers:function(listId, name){
            var that = this;
            var href = '{$urlHrefs.subscribersImportList}';
            href = GLOBAL.func.addUrlParam(href, 'listId', listId);
            $(that.dialog).dialog({
                title: '导入订阅者到集合' + name,
                iconCls: 'fa fa-upload',
                width: <?=$loginMobile?"'90%'":450?>,
                height: 450,
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[{
                    text:'关闭',
                    iconCls:iconClsDefs.cancel,
                    handler: function(){
                        $(that.dialog).dialog('close');
                        that.reload();
                        listsTreeModule.reload();
                    }
                }]
            });
            $(that.dialog).dialog('center');
        }
    };

</script>