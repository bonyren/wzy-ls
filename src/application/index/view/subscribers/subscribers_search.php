<?php
use app\index\Defs as IndexDefs;
?>
<table id="subscribersSearchDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    checkOnSelect:false,
    selectOnCheck:false,
    url:'{$urlHrefs.subscribersSearch}',
    method:'post',
    toolbar:'#subscribersSearchToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    rowStyler:subscribersSearchModule.rowStyler">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true"></th>
            <th data-options="field:'op',width:100,align:'center',formatter:subscribersSearchModule.operate">操作</th>
            <th data-options="field:'email',width:200,align:'center',sortable:true">邮箱</th>
            <th data-options="field:'blacklisted',width:60,align:'center',formatter:subscribersSearchModule.formatBlacklisted">黑名单</th>
            <th data-options="field:'lists_count_style',width:60,align:'center'">所在集合</th>
            <th data-options="field:'campaigns_count_style',width:60,align:'center'">投递活动</th>
            <th data-options="field:'bounce_count_style',width:60,align:'center'">退信</th>
            <th data-options="field:'modified',width:100,align:'center',sortable:true">最后修改</th>
        </tr>
    </thead>
</table>
<div id="subscribersSearchToolbar"  class="p-1">
    <form id="subscribersSearchToolbarForm">
        <span class="fa fa-search"></span>
        邮箱:
        <input id="subscribersSearchToolbarFormSearchbox" name="search[email]" class="easyui-textbox"
            data-options="width:200" />
        所属集合:
        <select class="easyui-combobox" name="search[list]" data-options="editable:false,width:200,panelHeight:200">
            <option value="0" selected>不限</option>
            <option value="">--无--</option>
            <?php foreach($lists as $list){ ?>
                <option value="<?=$list['list_id']?>"><?=$list['name']?></option>
            <?php } ?>
        </select>
        黑名单:
        <select id="blacklistedCombobox" name="search[blacklisted]" class="easyui-combobox" editable="false"
                panelHeight="auto"
                style="width:80px">
            <option value="" selected>不限</option>
            <?php foreach(IndexDefs::$eBooleanDefs as $key=>$value){
                echo '<option value="' . $key . '" >' . $value . '</option>';
            }?>
        </select>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
            onClick:function(){ subscribersSearchModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
            onClick:function(){ subscribersSearchModule.reset(); }">重置
        </a>
    </form>
    <div class="line my-1"></div>
    <div>
        <a href="#" class="easyui-linkbutton" 
            data-options="onClick:function(){ subscribersSearchModule.add(); },iconCls:'fa fa-plus'">新增
        </a>
        <a href="#" class="easyui-linkbutton" 
            data-options="onClick:function(){ subscribersSearchModule.downloadCSV(this); },iconCls:'fa fa-download'">下载所有到CSV文件
        </a>
        <a href="#" class="easyui-linkbutton"
           data-options="onClick:function(){ subscribersSearchModule.import(this); },iconCls:'fa fa-upload'">导入
        </a>
        <a href="#" class="easyui-linkbutton"
           data-options="onClick:function(){ subscribersSearchModule.deleteChecked(this); },iconCls:'fa fa-remove'">删除所有选中
        </a>
        <a href="#" class="easyui-linkbutton"
           data-options="onClick:function(){ subscribersSearchModule.joinLists(this); },iconCls:'fa fa-cubes'">选中加入集合
        </a>
    </div>
</div>
<script>
    var subscribersSearchModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#subscribersSearchDatagrid',
        searchForm:'#subscribersSearchToolbarForm',
        rowStyler:function(index, row){
            row.bounce_count_style = '<span class="text-warning my-1 font-weight-bold">' + row.bounce_count + '</span>';
            row.campaigns_count_style = '<span class="text-secondary my-1 font-weight-bold">' + row.campaigns_count + '</span>';
            row.lists_count_style = '<a href="javascript:;" onclick="subscribersSearchModule.showLists(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')"><span class="text-primary my-1 font-weight-bold text-underline">' + row.lists_count + '</span></a>';

            if(row.blacklisted){
               //return DG_ROW_CSS.rowGray;
                return DG_ROW_CSS.rowDel;
            }
        },
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="subscribersSearchModule.edit(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')" title="编辑"><i class="fa fa-pencil-square-o">编辑</i></a>');
            btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="subscribersSearchModule.delete(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\')" title="删除"><i class="fa fa-trash-o">删除</i></a>');
            return btns.join(' ');
        },
        formatBlacklisted:function(val, row){
            if(row.blacklisted){
                return '<span class="label label-default radius my-1"><a href="javascript:;" onclick="subscribersSearchModule.changeBlacklisted(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\', false)" title="移出黑名单"><i class="fa fa-check-square-o fa-lg"></i></a></span>';
            }else{
                return '<span class="radius my-1"><a href="javascript:;" onclick="subscribersSearchModule.changeBlacklisted(' + row.subscriber_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.email) + '\', true)" title="加入黑名单"><i class="fa fa-square-o fa-lg"></i></a></span>';
            }
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        search:function(that){
            var that = this;
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            //reset the query parameter
            $.each($(that.searchForm).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $.each($(that.searchForm).serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            that.load();
        },
        reset:function(){
            var that = this;
            $(that.searchForm).form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($(that.searchForm).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            that.load();
        },
        load:function(){
            $(this.datagrid).datagrid('load');
        },
        add:function(){
            var that = this;
            var href = '{$urlHrefs.subscribersSearchAdd}';
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
                        var $form = $(that.dialog).find('form').eq(0);
                        $form.form('submit', {
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
            var href = '{$urlHrefs.subscribersSearchEdit}';
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
                        var $form = $(that.dialog).find('form').eq(0);
                        $form.form('submit', {
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
        delete:function(subscriberId, title){
            var that = this;
            var href = '{$urlHrefs.subscribersSearchDelete}';
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
                    }
                }, 'json');
            });
        },
        downloadCSV:function(element){
            var that = this;
            var href = '{$urlHrefs.subscribersAllExport}';
            $(that.dialog).dialog({
                title: '导出订阅者',
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
                buttons:[],
                onClose:function(){
                    that.reload();
                }
            });
            $(that.dialog).dialog('center');
        },
        import:function(element){
            var that = this;
            var href = '{$urlHrefs.subscribersAllImport}';
            $(that.dialog).dialog({
                title: '导入订阅者',
                iconCls: 'fa fa-upload',
                width: 800,
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
        },
        deleteChecked:function(element){
            var that = this;
            var checkedSubscriberIds = [];
            var checkedRows = $(that.datagrid).datagrid('getChecked');
            for(var i= 0,count=checkedRows.length; i<count; i++){
                checkedSubscriberIds.push(checkedRows[i].subscriber_id);
            }
            if(checkedSubscriberIds.length == 0){
                $.messager.alert('提示', '请选择要删除的订阅者', 'info');
                return;
            }
            var href = '{$urlHrefs.subscribersDeleteChecked}';
            $.messager.confirm('提示', '确认删除所有选中的订阅者吗?', function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {subscriberIds:checkedSubscriberIds}, function(res){
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
        joinLists:function(element){
            //console.log(element);
            var that = this;
            var checkedSubscriberIds = [];
            var checkedRows = $(that.datagrid).datagrid('getChecked');
            for(var i= 0,count=checkedRows.length; i<count; i++){
                checkedSubscriberIds.push(checkedRows[i].subscriber_id);
            }
            if(checkedSubscriberIds.length == 0){
                $.messager.alert('提示', '请选择要加入的订阅者', 'info');
                return;
            }
            var href = '<?=url('Lists/chooseLists')?>';
            $(that.dialog).dialog({
                title: '请选择要加入的集合',
                iconCls: 'fa fa-cubes',
                width: <?=$loginMobile?"'90%'":450?>,
                height: 450,
                cache: false,
                href: href,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons:[
                    {
                        text:'确定',
                        iconCls:iconClsDefs.ok,
                        handler: function(){
                            var lists = chooseListsModule.getChoosedLists();
                            if(lists.length == 0){
                                $.messager.alert('提示', '请选择要加入的集合', 'info');
                                return;
                            }
                            var listIds = [];
                            lists.forEach(function(list){
                                listIds.push(list.list_id);
                            });
                            var url = "<?=url('Subscribers/subscribersSaveLists')?>";
                            $.messager.progress({text:'处理中，请稍候...'});
                            $.post(url, {subscriberIds:checkedSubscriberIds, listIds:listIds}, function(res){
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
                    }
                ]
            });
            $(that.dialog).dialog('center');
        },
        showLists:function(subscriberId, title){
            var that = this;
            var href = '<?=url('Lists/listsLight')?>';
            href = GLOBAL.func.addUrlParam(href, 'subscriberId', subscriberId);
            $(that.dialog).dialog({
                title: `"${title}"所加入的集合列表`,
                iconCls: 'fa fa-cubes',
                width: <?=$loginMobile?"'90%'":450?>,
                height: 200,
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
        }
    };
    
</script>