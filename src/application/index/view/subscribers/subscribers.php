<?php
use app\index\Defs as IndexDefs;
?>
<div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'north',border:false" style="height:70%;">
        <table id="subscribersDatagrid" class="easyui-datagrid" data-options="striped:true,
            nowrap:false,
            rownumbers:true,
            autoRowHeight:true,
            singleSelect:true,
            checkOnSelect:false,
            selectOnCheck:false,
            url:'{$urlHrefs.subscribers}',
            method:'post',
            toolbar:'#subscribersToolbar',
            pagination:true,
            pageSize:<?=DEFAULT_PAGE_ROWS?>,
            pageList:[10,20,30,50,80,100],
            border:false,
            fit:true,
            fitColumns:<?=$loginMobile?'false':'true'?>,
            rowStyler:subscribersModule.rowStyler
            ">
            <thead>
                <tr>
                    <th data-options="field:'ck',checkbox:true"></th>
                    <th data-options="field:'op',width:150,align:'center',formatter:subscribersModule.operate">操作</th>
                    <th data-options="field:'email',width:200,align:'center',sortable:true">邮箱</th>
                    <th data-options="field:'blacklisted',width:100,align:'center',formatter:subscribersModule.formatBlacklisted">黑名单</th>
                    <th data-options="field:'campaigns_count_style',width:60,align:'center'">投递活动</th>
                    <th data-options="field:'bounce_count_style',width:60,align:'center'">退信</th>
                    <th data-options="field:'modified',width:120,align:'center',sortable:true">最后修改</th>
                </tr>
            </thead>
        </table>
        <div id="subscribersToolbar" class="p-1">
            <form id="subscribersToolbarForm">
                邮箱:
                <input id="subscribersToolbarFormSearchbox" name="search[email]" class="easyui-textbox"
                              data-options="width:200" />
                黑名单:
                <select id="blacklistedCombobox" name="search[blacklisted]" class="easyui-combobox"
                        editable="false"
                        panelHeight="auto"
                        style="width:80px">
                    <option value="" selected>不限</option>
                    <?php foreach(IndexDefs::$eBooleanDefs as $key=>$value){
                        echo '<option value="' . $key . '" >' . $value . '</option>';
                    }?>
                </select>
                <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                    onClick:function(){ subscribersModule.search(); }">搜索
                </a>
                <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                    onClick:function(){ subscribersModule.reset(); }">重置
                </a>
            </form>
            <div class="line my-1"></div>
            <div>
                <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ subscribersModule.add(); },iconCls:'fa fa-plus'">新增</a>
                <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ subscribersModule.download(); },iconCls:'fa fa-download'">下载</a>
                <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ subscribersModule.import(); },iconCls:'fa fa-upload'">导入</a>
            </div>
        </div>
    </div>
    <div data-options="region:'center', title:'操作', iconCls:'fa fa-wrench', border:true">
        <form id="subscribersActionForm" method="post">
            <table class="table-form">
                <tr>
                    <td class="field-label" style="width:20%;">操作目标</td>
                    <td class="field-input" colspan="2">
                        <div class="m-1">
                            <input id="actionTargetTagsRadio" class="easyui-radiobutton" name="subscribersTarget"
                                   data-options="labelWidth:150,value:'<?=IndexDefs::eSubscriberActionTargetTags?>',checked:true,label:'选中的订阅者',labelPosition:'after'">
                            <input class="easyui-radiobutton" name="subscribersTarget"
                                   data-options="labelWidth:150,value:'<?=IndexDefs::eSubscriberActionTargetAll?>',checked:false,label:'该集合下的所有订阅者',labelPosition:'after'">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field-label">
                        操作动作
                    </td>
                    <td class="field-input">
                        <div class="mx-1">
                            <div class="my-1">
                                <input class="easyui-radiobutton" name="subscribersAction"
                                       label="从该集合中移除" labelPosition="after" labelWidth="150"
                                       value="<?=IndexDefs::eSubscriberActionDelete?>"
                                       data-options="onChange:function(checked){subscribersModule.onActionChange(<?=IndexDefs::eSubscriberActionDelete?>, checked)}"/>
                                <input class="easyui-radiobutton" name="subscribersAction" value="<?=IndexDefs::eSubscriberActionMove?>"
                                    data-options="onChange:function(checked){subscribersModule.onActionChange(<?=IndexDefs::eSubscriberActionMove?>, checked)}"/>
                                移动到
                                <select id="subscribersMoveActionList" name="subscribersMoveActionList" class="easyui-combobox"
                                        data-options="disabled:true" editable="false" panelHeight="auto" style="width:200px">
                                    {volist name="$bindValues.otherLists" id="list"}
                                    <option value="{$list.list_id}">{$list.name}</option>
                                    {/volist}
                                </select>
                            </div>
                            <div class="my-1">
                                <input class="easyui-radiobutton" name="subscribersAction"
                                       label="无" labelPosition="after" labelWidth="150"
                                       value="<?=IndexDefs::eSubscriberActionNothing?>"
                                       data-options="onChange:function(checked){subscribersModule.onActionChange(<?=IndexDefs::eSubscriberActionNothing?>, checked)}" checked/>
                                <input class="easyui-radiobutton" name="subscribersAction" value="<?=IndexDefs::eSubscriberActionCopy?>"
                                    data-options="onChange:function(checked){subscribersModule.onActionChange(<?=IndexDefs::eSubscriberActionCopy?>, checked)}"/>
                                复制到
                                <select id="subscribersCopyActionList" name="subscribersCopyActionList" class="easyui-combobox"
                                        data-options="disabled:true" editable="false" panelHeight="auto" style="width:200px">
                                    {volist name="$bindValues.otherLists" id="list"}
                                    <option value="{$list.list_id}">{$list.name}</option>
                                    {/volist}
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a id="subscribersActionBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-gavel',
                            onClick:function(){
                                subscribersModule.action();
                            }">执行</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script>
    var subscribersModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#subscribersDatagrid',
        searchForm:'#subscribersToolbarForm',
        listId: {$bindValues.listId},
        listName: '{$bindValues.listName}',
        rowStyler:function(index, row){
            row.bounce_count_style = '<span class="text-warning my-1">' + row.bounce_count + '</span>';
            row.campaigns_count_style = '<span class="text-secondary my-1">' + row.campaigns_count + '</span>';
            if(row.blacklisted){
                //return DG_ROW_CSS.rowGray;
                return DG_ROW_CSS.rowDel;
            }
        },
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="subscribersModule.edit(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')" title="编辑"><i class="fa fa-pencil-square-o fa-lg">编辑</i></a>');
            <?php if($bindValues['listId'] != -1){ ?>
            btns.push('<a href="javascript:;" class="btn btn-outline-warning size-MINI radius my-1" onclick="subscribersModule.remove(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')" title="移除"><i class="fa fa-remove fa-lg">移除</i></a>');
            <?php } ?>
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="subscribersModule.delete(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')" title="删除"><i class="fa fa-trash-o fa-lg">删除</i></a>');
            return btns.join(' ');
        },
        formatBlacklisted:function(val, row){
            if(row.blacklisted){
                return '<span class="label label-default radius my-1"><a href="javascript:;" onclick="subscribersModule.changeBlacklisted(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\', false)" title="移出黑名单"><i class="fa fa-check-square-o fa-lg"></i></a></span>';
            }else{
                return '<span class="radius my-1"><a href="javascript:;" onclick="subscribersModule.changeBlacklisted(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\', true)" title="加入黑名单"><i class="fa fa-square-o fa-lg"></i></a></span>';
            }
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        search:function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            //reset the query parameter
            $.each($("#subscribersToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $.each($("#subscribersToolbarForm").serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            $(this.datagrid).datagrid('load');
        },
        reset:function(){
            var that = this;
            $("#subscribersToolbarForm").form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($("#subscribersToolbarForm").serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $(this.datagrid).datagrid('load');
        },
        add:function(){
            var that = this;
            var href = '{$urlHrefs.subscribersAdd}';
            $(that.dialog).dialog({
                title: '新增订阅者',
                iconCls: 'fa fa-plus-circle',
                width: 500,
                height: '60%',
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
                                $.post(href, addByDialogModule.serializeFormString(), function(res){
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
        edit:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersEdit}';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $(that.dialog).dialog({
                title: '修改订阅者 - ' + title,
                iconCls: 'fa fa-pencil-square',
                width: 500,
                height: '60%',
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
                                $.post(href, editByDialogModule.serializeFormString(), function(res){
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
        remove:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersRemove}';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $.messager.confirm('提示', `确认从该集合移除"${title}"吗？`, function(result){
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
        delete:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersDelete}';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $.messager.confirm('提示', `确认删除"${title}"吗？`, function(result){
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
        changeBlacklisted:function(subscriberId, title, blacklisted){
            var that = this;
            if(blacklisted){
                that.blacklist(subscriberId, title);
            }else{
                that.whitelist(subscriberId, title);
            }
        },
        whitelist:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersWhitelist}';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $.messager.confirm('提示', `确认将"${title}"移出黑名单吗？`, function(result){
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
        blacklist:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersBlacklist}';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $.messager.confirm('提示', `确认将"${title}"加入黑名单吗？`, function(result){
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
        download:function(){
            var that = this;
            var href = '{$urlHrefs.subscribersExportList}';
            $(that.dialog).dialog({
                title: '从集合' + that.listName + '导出订阅者',
                iconCls: 'fa fa-download',
                width: 800,
                height: '90%',
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[]
            });
            $(that.dialog).dialog('center');
        },
        import:function(){
            var that = this;
            var href = '{$urlHrefs.subscribersImportList}';
            $(that.dialog).dialog({
                title: '导入订阅者到集合' + that.listName,
                iconCls: 'fa fa-upload',
                width: 900,
                height: '60%',
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
                    listsTreeModule.reload();
                }
            });
            $(that.dialog).dialog('center');
        },
        onActionChange:function(action, checked){
            if(action == <?=IndexDefs::eSubscriberActionMove?> && checked){
                $("#subscribersMoveActionList").combobox('enable');
                $("#subscribersCopyActionList").combobox('disable');
            }else if(action == <?=IndexDefs::eSubscriberActionCopy?> && checked){
                $("#subscribersMoveActionList").combobox('disable');
                $("#subscribersCopyActionList").combobox('enable');
            }else{
                $("#subscribersMoveActionList").combobox('disable');
                $("#subscribersCopyActionList").combobox('disable');
            }
        },
        action:function(){
            var that = this;
            var checkedSubscriberIds = [];
            var checkedRows = $(that.datagrid).datagrid('getChecked');
            for(var i= 0,count=checkedRows.length; i<count; i++){
                checkedSubscriberIds.push(checkedRows[i].subscriber_id);
            }
            var targetTags = $('#actionTargetTagsRadio').radiobutton('options').checked;
            if(targetTags && checkedRows.length == 0){
                $.app.method.alertError(null, '请选择要操作的订阅者');
                return;
            }
            var url = '{$urlHrefs.subscribersAction}';

            $.messager.confirm('提示', '确认执行该操作吗？', function(result) {
                if (!result) return false;
                $("#subscribersActionForm").form('submit', {
                    url:url,
                    queryParams:{subscriberIds:checkedSubscriberIds.join(',')},
                    onSubmit:function(param){
                        $.messager.progress({text:'处理中，请稍候...'});
                        return true;
                    },
                    success:function(data){
                        $.messager.progress('close');
                        var obj = JSON.parse(data);
                        if(!obj.code){
                            $.app.method.alertError(null, obj.msg);
                        }else{
                            $.app.method.tip('提示', obj.msg, 'info');
                        }
                        that.reload();
                        listsTreeModule.reload();
                    },
                    onBeforeLoad:function(param){
                        alert('onBeforeLoad');
                    },
                    onLoadSuccess:function(data){
                        //alert(data.code);
                        alert('onLoadSuccess');
                    },
                    onLoadError:function(){
                        alert('onLoadError');
                    }
                });
            });
        }
    };
</script>