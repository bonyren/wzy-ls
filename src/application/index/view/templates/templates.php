<?php
use app\index\model\Templates as TemplatesModel;

?>
<table id="templatesDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.templates}',
    method:'post',
    toolbar:'#templatesToolbar',
    pagination:true,
    pageSize:<?= DEFAULT_PAGE_ROWS ?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?= $loginMobile ? 'false' : 'true' ?>
    ">
    <thead>
    <tr>
        <th data-options="field:'operate',width:200,align:'center',formatter:templatesModule.operate">操作</th>
        <th data-options="field:'title',width:200,align:'center'">标题</th>
        <th data-options="field:'default',width:100,align:'center',formatter:templatesModule.formatDefault">默认</th>
        <th data-options="field:'system',width:100,align:'center',formatter:templatesModule.formatSystem">系统</th>
    </tr>
    </thead>
</table>
<div id="templatesToolbar" class="p-1">
    <div>
        <a href="#" class="easyui-linkbutton"
           data-options="onClick:function(){ templatesModule.add(); },iconCls:'fa fa-plus'">新增</a>
    </div>
</div>
<script>
    var templatesModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#templatesDatagrid',
        operate: function (val, row) {
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius" onclick="templatesModule.edit(' + row.template_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="编辑"><i class="fa fa-pencil-square-o fa-lg">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius" onclick="templatesModule.delete(' + row.template_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="删除"><i class="fa fa-trash-o fa-lg">删除</i></a>');
            return btns.join(' ');
        },
        formatDefault: function (val, row) {
            if (row.default == <?=TemplatesModel::eTemplateDefault?>) {
                return '<i class="fa fa-dot-circle-o fa-lg"></i>';
            } else {
                return '<a href="javascript:;" onclick="templatesModule.default(' + row.template_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="Default the template"><i class="fa fa-circle-o fa-lg"></i></a>';
            }
        },
        formatSystem: function (val, row) {
            if (row.system == <?=TemplatesModel::eTemplateSystemYes?>) {
                return '<i class="fa fa-dot-circle-o fa-lg"></i>';
            } else {
                return '<a href="javascript:;" onclick="templatesModule.system(' + row.template_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.title) + '\')" title="System the template"><i class="fa fa-circle-o fa-lg"></i></a>';
            }
        },
        default: function (templateId, title) {
            var that = this;
            var href = '{$urlHrefs.templatesMakeDefault}';
            href = GLOBAL.func.addUrlParam(href, 'templateId', templateId);
            var confirmStr = `确认设置"${title}"为默认模板吗?`;
            $.messager.confirm('提示', confirmStr, function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post(href, {}, function (res) {
                    $.messager.progress('close');
                    if (!res.code) {
                        $.app.method.alertError(null, res.msg);
                    } else {
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                    }
                }, 'json');
            });
        },
        system: function (templateId, title) {
            var that = this;
            var href = '{$urlHrefs.templatesMakeSystem}';
            href = GLOBAL.func.addUrlParam(href, 'templateId', templateId);
            var confirmStr = `确认设置"${title}"为系统模板吗?`;
            $.messager.confirm('提示', confirmStr, function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post(href, {}, function (res) {
                    $.messager.progress('close');
                    if (!res.code) {
                        $.app.method.alertError(null, res.msg);
                    } else {
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                    }
                }, 'json');
            });
        },
        reload: function () {
            $(this.datagrid).datagrid('reload');
        },
        reset: function () {
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            for (var key in queryParams) {
                delete queryParams[key];
            }
            $(that.datagrid).datagrid('load');
        },
        add: function () {
            var that = this;
            var href = '{$urlHrefs.templatesAdd}';
            $(that.dialog).dialog({
                title: '新增模板',
                iconCls: 'fa fa-plus-circle',
                width: '80%',
                height: '100%',
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: iconClsDefs.ok,
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;
                                $.messager.progress({text: '处理中，请稍候...'});
                                $.post(href, templatesAddModule.serializeForm(), function (res) {
                                    $.messager.progress('close');
                                    if (!res.code) {
                                        $.app.method.alertError(null, res.msg);
                                    } else {
                                        $.app.method.tip('提示', res.msg, 'info');
                                        $(that.dialog).dialog('close');
                                        that.reload();
                                    }
                                }, 'json');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: iconClsDefs.cancel,
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
            $(that.dialog).dialog('center');
        },
        edit: function (templateId, title) {
            var that = this;
            var href = '{$urlHrefs.templatesEdit}';
            href = GLOBAL.func.addUrlParam(href, 'templateId', templateId);
            $(that.dialog).dialog({
                title: '修改模板 - ' + title,
                iconCls: 'fa fa-pencil-square',
                width: '80%',
                height: '100%',
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: iconClsDefs.ok,
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;
                                $.messager.progress({text: '处理中，请稍候...'});
                                $.post(href, templatesEditModule.serializeForm(), function (res) {
                                    $.messager.progress('close');
                                    if (!res.code) {
                                        $.app.method.alertError(null, res.msg);
                                    } else {
                                        $.app.method.tip('提示', res.msg, 'info');
                                        $(that.dialog).dialog('close');
                                        that.reload();
                                    }
                                }, 'json');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: iconClsDefs.cancel,
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
            $(that.dialog).dialog('center');
        },
        delete: function (templateId, title) {
            var that = this;
            var href = '{$urlHrefs.templatesDelete}';
            href = GLOBAL.func.addUrlParam(href, 'templateId', templateId);
            $.messager.confirm('提示', `确认删除该模板"${title}"吗?`, function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post(href, {}, function (res) {
                    $.messager.progress('close');
                    if (!res.code) {
                        $.app.method.alertError(null, res.msg);
                    } else {
                        $.app.method.tip('提示', res.msg, 'info');
                        that.reload();
                    }
                }, 'json');
            });
        }
    };
</script>