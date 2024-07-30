<?php
use app\index\model\Admins as AdminsModel;
?>
<table id="adminsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    <?php if(isset($_GET['dialog_call']) && $_GET['dialog_call'] && $_GET['multiple']): ?>
    selectOnCheck:false,
    checkOnSelect:false,
    <?php endif; ?>
    url:'{$urlHrefs.admins}',
    method:'post',
    toolbar:'#adminsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:'',
    rowStyler:adminsModule.rowStyler.bind(adminsModule)">
    <thead>
    <tr>
        <?php if(isset($_GET['dialog_call']) && $_GET['dialog_call'] && $_GET['multiple']): ?>
        <th field="ck" checkbox="true"></th>
        <?php endif; ?>
        <th data-options="field:'operate',width:200,fixed:true,align:'center',formatter:adminsModule.operate.bind(adminsModule)">操作</th>
        <th data-options="field:'realname',width:200,align:'center'">姓名</th>
        <th data-options="field:'login_name',width:200,align:'center'">登录名</th>
        <th data-options="field:'email',width:200,align:'center'">邮箱</th>
        <th data-options="field:'super_user',width:200,align:'center',formatter:adminsModule.formatSuper.bind(adminsModule)">类型</th>
        <th data-options="field:'role_name',width:100,align:'center'">角色</th>
        <th data-options="field:'disabled',width:200,align:'center',formatter:adminsModule.formatDisabled.bind(adminsModule)">状态</th>
    </tr>
    </thead>
</table>
<div id="adminsToolbar" class="p-1">
    <div>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ adminsModule.add(); },iconCls:iconClsDefs.add">添加用户</a>
    </div>
    <div class="line my-1"></div>
    <form id="adminsToolbarForm">
        姓名: <input name="search[realname]" class="easyui-textbox" data-options="width:100" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                        onClick:function(){ adminsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                        onClick:function(){ adminsModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var adminsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#adminsDatagrid',
        searchForm:'#adminsToolbarForm',
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="adminsModule.edit(' + row.admin_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.realname) + '\')" title="编辑"><i class="fa fa-pencil-square-o fa-lg">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="adminsModule.delete(' + row.admin_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.realname) + '\')" title="删除"><i class="fa fa-trash-o fa-lg">删除</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius my-1" onclick="adminsModule.changePwd(' + row.admin_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.realname) + '\')" title="修改密码"><i class="fa fa-key fa-lg">修改密码</i></a>');
            return btns.join(' ');
        },
        rowStyler:function (index, row) {
            //每一行会被调用两次
            if(row.disabled == <?=AdminsModel::eAdminDisabledStatus?>){
                return DG_ROW_CSS.rowGray;
            }
        },
        formatSuper:function(val, row){
            if(val == <?=AdminsModel::eAdminSuperRole?>){
                return '<span class="badge badge-success radius">超级用户</span>';
            }else{
                return '<span class="badge badge-default radius">普通用户</span>';
            }
        },
        formatDisabled:function(val, row){
            if(val == <?=AdminsModel::eAdminEnableStatus?>){
                return '<span class="badge badge-success radius">有效</span>';
            }else{
                return '<span class="badge badge-default radius">无效</span>';
            }
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
        },
        add:function(){
            var that = this;
            var href = '{$urlHrefs.adminsAdd}';
            $(that.dialog).dialog({
                title: '添加管理员',
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
                        var $form = $(that.dialog).find('form').eq(0);
                        var isValid = $form.form('validate');
                        if (!isValid) return;
                        $.messager.progress({text:'处理中，请稍候...'});
                        $.post(href, $form.serialize(), function(res){
                            $.messager.progress('close');
                            if(!res.code){
                                $.app.method.alertError(null, res.msg);
                            }else{
                                $.app.method.tip('提示', res.msg, 'info');
                                $(that.dialog).dialog('close');
                                that.reload();
                            }
                        }, 'json');
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
        edit:function(adminId, title){
            var that = this;
            var href = '{$urlHrefs.adminsEdit}';
            href = GLOBAL.func.addUrlParam(href, 'adminId', adminId);
            $(that.dialog).dialog({
                title: '修改用户 - ' + title,
                iconCls: iconClsDefs.edit,
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
                        var $form = $(that.dialog).find('form').eq(0);
                        var isValid = $form.form('validate');
                        if (!isValid) return;
                        $.messager.progress({text:'处理中，请稍候...'});
                        $.post(href, $form.serialize(), function(res){
                            $.messager.progress('close');
                            if(!res.code){
                                $.app.method.alertError(null, res.msg);
                            }else{
                                $.app.method.tip('提示', res.msg, 'info');
                                $(that.dialog).dialog('close');
                                that.reload();
                            }
                        }, 'json');
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
        delete:function(adminId, title){
            var that = this;
            var href = '{$urlHrefs.adminsDelete}';
            href = GLOBAL.func.addUrlParam(href, 'adminId', adminId);
            $.messager.confirm('提示', `确认删除"${title}"吗?`, function(result){
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
        changePwd:function(adminId, title){
            var that = this;
            var href = '{$urlHrefs.adminsChangePwd}';
            href = GLOBAL.func.addUrlParam(href, 'adminId', adminId);
            $(that.dialog).dialog({
                title: `修改管理员"${title}"密码`,
                iconCls: iconClsDefs.edit,
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
                        var $form = $(that.dialog).find('form').eq(0);
                        var isValid = $form.form('validate');
                        if (!isValid) return;
                        $.messager.progress({text:'处理中，请稍候...'});
                        $.post(href, $form.serialize(), function(res){
                            $.messager.progress('close');
                            if(!res.code){
                                $.app.method.alertError(null, res.msg);
                            }else{
                                $.app.method.tip('提示', res.msg, 'info');
                                $(that.dialog).dialog('close');
                                that.reload();
                            }
                        }, 'json');
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
        }
    };

</script>