<?php
use app\index\model\Mailboxes as MailboxesModel;
?>
<table id="mailboxesDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.mailboxes}',
    method:'post',
    toolbar:'#mailboxesToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:''
    ">
    <thead>
    <tr>
        <th data-options="field:'operate',width:200,align:'center',formatter:mailboxesModule.operate">操作</th>
        <th data-options="field:'title',width:200,align:'center'">标题</th>
        <th data-options="field:'default',width:50,align:'center',formatter:mailboxesModule.formatDefault">默认</th>
        <th data-options="field:'system',width:50,align:'center',formatter:mailboxesModule.formatSystem">系统</th>
    </tr>
    </thead>
</table>
<div id="mailboxesToolbar" class="p-1">
    <div>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ mailboxesModule.add(); },iconCls:'fa fa-plus'">新增</a>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ mailboxesModule.export(); },iconCls:'fa fa-mail-forward'">导出</a>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ mailboxesModule.import(); },iconCls:'fa fa-mail-reply'">导入</a>
    </div>
</div>
<script>
    var mailboxesModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#mailboxesDatagrid',
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="mailboxesModule.edit(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="编辑"><i class="fa fa-pencil-square-o fa-lg">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="mailboxesModule.delete(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="删除"><i class="fa fa-trash-o fa-lg">删除</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-info size-MINI radius my-1" onclick="mailboxesModule.testOutgoingServer(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="测试发送服务器"><i class="fa fa-flask fa-lg">测试发送服务器</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-info size-MINI radius my-1" onclick="mailboxesModule.testIncomingServer(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="测试接收服务器"><i class="fa fa-flask fa-lg">测试接收服务器</i></a>');
            return btns.join(' ');
        },
        formatDefault:function(val, row){
            if(row.default == <?=MailboxesModel::eMailboxDefault?>){
                return '<i class="fa fa-dot-circle-o fa-lg"></i>';
            }else{
                return '<a href="javascript:;" onclick="mailboxesModule.default(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="设置为默认邮箱"><i class="fa fa-circle-o fa-lg"></i></a>';
            }
        },
        formatSystem:function(val, row){
            if(row.system == <?=MailboxesModel::eMailboxSystemYes?>){
                return '<i class="fa fa-dot-circle-o fa-lg"></i>';
            }else{
                return '<a href="javascript:;" onclick="mailboxesModule.system(' + row.mailbox_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="设置为系统邮箱"><i class="fa fa-circle-o fa-lg"></i></a>';
            }
        },
        default:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesMakeDefault}';
            href = GLOBAL.func.addUrlParam(href, 'mailboxId', mailboxId);
            var confirmStr = `确认设置"${title}"为默认邮箱吗?`;
            $.messager.confirm('提示', confirmStr, function(result){
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
        system:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesMakeSystem}';
            href = GLOBAL.func.addUrlParam(href, 'mailboxId', mailboxId);
            var confirmStr = `确认设置"${title}"为系统邮箱吗?`;
            $.messager.confirm('提示', confirmStr, function(result){
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
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        reset:function(){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            for(var key in queryParams){
                delete queryParams[key];
            }
            $(this.datagrid).datagrid('load');
        },
        add:function(){
            var that = this;
            var href = '{$urlHrefs.mailboxesAdd}';
            $(that.dialog).dialog({
                title: '新增邮箱配置',
                iconCls: 'fa fa-plus-circle',
                width: <?=$loginMobile?"'90%'":500?>,
                height: '90%',
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
        edit:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesEdit}';
            href = GLOBAL.func.addUrlParam(href, 'mailboxId', mailboxId);
            $(that.dialog).dialog({
                title: '修改邮箱配置 - ' + title,
                iconCls: 'fa fa-pencil-square',
                width: <?=$loginMobile?"'90%'":500?>,
                height: '90%',
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
        delete:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesDelete}';
            href = GLOBAL.func.addUrlParam(href, 'mailboxId', mailboxId);
            $.messager.confirm('提示', `确认删除该邮箱配置"${title}"吗?`, function(result){
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
        testOutgoingServer:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesTestOutgoingServer}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {
                mailboxId:mailboxId
            }, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.alert(null, '发送服务器正常');
                }
            }, 'json');
        },
        testIncomingServer:function(mailboxId, title){
            var that = this;
            var href = '{$urlHrefs.mailboxesTestIncomingServer}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {
                mailboxId:mailboxId
            }, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.alert(null, '接收服务器正常');
                }
            }, 'json');
        },
        export:function(){
            var that = this;
            var href = '{$urlHrefs.mailboxesExport}';
            window.open(href);
        },
        import:function(){
            var that = this;
            $.messager.confirm('提示', `导入的文件必须是从本系统导出的文件，其他系统生成的文件不支持，此操作将覆盖所有已有的设置，请慎重选择！确认要操作吗？`, function(result) {
                if (!result) return false;
                $.app.method.uploadOne("<?=url('index/Upload/uploadImport')?>",
                    function (res) {
                        if (res.code) {
                            var saveName = res.data.save_name;
                            //post request
                            var url = '{$urlHrefs.mailboxesImport}';
                            $.messager.progress({text: '处理中，请稍候...'});
                            $.post(url, {
                                saveName: saveName
                            }, function (res) {
                                $.messager.progress('close');
                                if (!res.code) {
                                    $.app.method.alertError(null, res.msg);
                                } else {
                                    $.app.method.tip('提示', res.msg, 'info');
                                    that.reload();
                                }
                            }, 'json');
                        } else {
                            $.app.method.tip('提示', (res.msg || 'failed to upload'), 'error');
                        }
                    },
                    function (filename) {  //上传验证函数
                        if (!filename.match(/\.csv$|\.xls$|\.xlsx$/i)) {
                            $.app.method.tip('提示', 'Upload file suffix not allowed', 'error');
                            return false;
                        }
                        return true;
                    }
                );
            });
        }
    };

</script>