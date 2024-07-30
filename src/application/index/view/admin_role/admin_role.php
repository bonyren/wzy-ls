<table id="adminRoleDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.adminRole}',
    method:'post',
    toolbar:'#adminRoleToolbar',
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
        <th data-options="field:'operate',width:200,fixed:true,align:'center',formatter:adminRoleModule.operate">操作</th>
        <th data-options="field:'role_name',width:200,fixed:true,align:'center'">角色名</th>
        <th data-options="field:'description',width:200,align:'center'">描述</th>
    </tr>
    </thead>
</table>
<div id="adminRoleToolbar" class="p-1">
    <div>
        <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ adminRoleModule.save(0); },iconCls:iconClsDefs.add">添加新角色</a>
    </div>
    <div class="line my-1"></div>
    <form id="adminRoleToolbarForm">
        角色名: <input name="search[role_name]" class="easyui-textbox" data-options="width:100" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                        onClick:function(){ adminRoleModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                        onClick:function(){ adminRoleModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var adminRoleModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#adminRoleDatagrid',
        searchForm:'#adminRoleToolbarForm',
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="adminRoleModule.save(' + row.role_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.role_name) + '\')" title="编辑"><i class="fa fa-pencil-square-o fa-lg">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="adminRoleModule.delete(' + row.role_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.role_name) + '\')" title="删除"><i class="fa fa-trash-o fa-lg">删除</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius my-1" onclick="adminRoleModule.authorize(' + row.role_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.role_name) + '\')" title="授权"><i class="fa fa-hand-o-right fa-lg">授权</i></a>');
            return btns.join(' ');
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
        save:function(roleId, title){
            var that = this;
            var href = '{$urlHrefs.save}';
            href = GLOBAL.func.addUrlParam(href, 'roleId', roleId);
            if(roleId){
                title = `修改角色 - ${title}`;
            }else{
                title = "新增角色";
            }
            $(that.dialog).dialog({
                title: title,
                iconCls: roleId?iconClsDefs.edit:iconClsDefs.add,
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
        delete:function(roleId, title){
            var that = this;
            var href = '{$urlHrefs.delete}';
            href += href.indexOf('?') != -1 ? '&roleId=' + roleId : '?roleId='+roleId;
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
        authorize:function(roleId, title){
            var that = this;
            var href = '{$urlHrefs.authorize}';
            href += href.indexOf('?') != -1 ? '&roleId='+roleId : '?roleId='+roleId;
            var that = this;
            $(that.dialog).dialog({
                title: `角色授权 - ${title}`,
                iconCls: 'fa fa-hand-o-right',
                width: <?=$loginMobile?"'90%'":600?>,
                height: '100%',
                cache: false,
                href: href,
                modal: true,
                buttons:[{
                    text:'确定',
                    iconCls:iconClsDefs.ok,
                    handler:function(){
                        var nodes = $(that.dialog).find('#authUserNodeTree').tree('getChecked');
                        if (!nodes.length) {
                            $.app.method.alertError(null, '请选择访问权限');
                            return false;
                        }
                        var nodeIds = [];
                        for (var i in nodes) {
                            if (nodes[i]['pid'] !== '0') {
                                nodeIds.push(nodes[i]['id']);
                            }
                        }
                        $.messager.progress({text:'处理中，请稍候...'});
                        $.post(href, {nodeIds:nodeIds}, function(res){
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
                    text: '取消',
                    iconCls: iconClsDefs.cancel,
                    handler: function(){
                        $(that.dialog).dialog('close');
                    }
                }]
            });
            $(that.dialog).dialog('center');
        }
    };
</script>