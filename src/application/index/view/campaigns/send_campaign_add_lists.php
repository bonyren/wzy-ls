<?php
use app\index\Defs as IndexDefs;
?>
<div class="easyui-panel" data-options="fit:true,border:false">
    <header>
        <div class="d-flex justify-content-end align-items-center">
            <div class="mr-5">
                <a class="easyui-linkbutton" href="#"  data-options="size:'large',iconCls:'fa fa-save',
                            onClick:function(){
                                sendCampaignAddListsModule.save();
                            }">保存
                </a>
            </div>
        </div>
    </header>
    <form id="sendCampaignAddSchedulingForm" method="post">
        <table class="table-form">
            <tr>
                <td class="field-label py-1" width="15%"><label>开始投递时间:</label></td>
                <td class="field-input">
                    <input class="easyui-datetimebox" name="embargo" data-options="required:true,editable:false" style="width:180px;" value="{$bindValues.embargo}"/>
                </td>
            </tr>
            <tr>
                <td class="field-label py-1"><label>停止投递时间:</label></td>
                <td class="field-input">
                    <input class="easyui-datetimebox" name="stop_after" data-options="required:true,editable:false" style="width:180px;" value="{$bindValues.stop_after}"/>
                </td>
            </tr>
            <tr>
                <td class="field-label py-1"><label>再次投递:</label></td>
                <td class="field-input">
                    <select name="requeue_interval" class="easyui-combobox"
                            data-options="editable:false,panelHeight:'auto',value:'<?=$bindValues['requeue_interval']?>'" style="width:180px;">
                        <?php foreach(IndexDefs::$eRequeueIntervalDefs as $key=>$interval){ ?>
                            <option value="<?=$key?>"><?=$interval?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
    <div id="selectedListsToolbar" class="p-1">
        <a href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-plus-square',
                            onClick:function(){
                                sendCampaignAddListsModule.chooseLists();
                            }">选择
        </a>
    </div>
    <table id="selectedListsDatagrid" class="easyui-datagrid" data-options="url:'{$urlHrefs.campaignSelectedListsDatagrid}',
                title:'投递目标订阅者集合',
                method:'post',
                animate:true,
                fit:true,
                fitColumns:true,
                striped:true,
                nowrap:false,
                rownumbers:false,
                autoRowHeight:true,
                singleSelect:false,
                border:false,
                pagination:false,
                toolbar:'#selectedListsToolbar',
                showFooter:false">
        <thead>
        <tr>
            <th data-options="field:'operate',width:60,align:'center',formatter:sendCampaignAddListsModule.selectedListsOperate">操作</th>
            <th data-options="field:'name',width:200,align:'center',formatter:sendCampaignAddListsModule.formatListName">集合</th>
            <th data-options="field:'formatMembers',width:60,align:'center',formatter:sendCampaignAddListsModule.formatMembers">订阅者数量</th>
        </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    var sendCampaignAddListsModule = {
        dialog: '#globel-dialog2-div',
        datagrid:'#selectedListsDatagrid',
        chooseLists:function(){
            var that = this;
            var href = '{$urlHrefs.chooseLists}';
            $(that.dialog).dialog({
                title: '选择集合',
                iconCls: 'fa fa-list-ul',
                width: '60%',
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
                        var lists = chooseListsModule.getChoosedLists();
                        for(var i= 0,len=lists.length; i<len; i++){
                            that.insertListIntoSelected(lists[i]);
                        }
                        $(that.dialog).dialog('close');
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
        selectedListsOperate:function(val, row, index){
            var btns = [];
            btns.push('<a href="javascript:;" onclick="sendCampaignAddListsModule.removeListFromSelected(' + row.list_id + ')" title="移除"><i class="fa fa-trash-o fa-lg"></i>移除</a>');
            return btns.join(' ');
        },
        insertListIntoSelected:function(row){
            var that = this;
            var listId = row.list_id;
            var nowRows = $(that.datagrid).datagrid('getRows');
            var bFind = false;
            for(var i= 0,len=nowRows.length; i<len; i++){
                if(listId == nowRows[i].list_id){
                    bFind = true;
                    break;
                }
            }
            if(!bFind){
                $(that.datagrid).datagrid('insertRow', {
                    row: row
                });
            }
        },
        removeListFromSelected:function(listId){
            var that = this;
            var rows = $(that.datagrid).datagrid('getRows');
            for(var i= 0,count=rows.length; i<count; i++){
                var row = rows[i];
                if(row.list_id == listId) {
                    var index = $(that.datagrid).datagrid('getRowIndex', row);
                    $(that.datagrid).datagrid('deleteRow', index);
                }
            }
        },
        formatListName:function(val, row){
            return row.name;
        },
        formatMembers:function(val, row){
            return  '<span class="badge badge-success" title="订阅者数量">' +  row.subscriber_count + '</span>';
        },
        getLists:function(){
            var that = this;
            var listIds = [];
            var rows = $(that.datagrid).datagrid('getRows');
            for(var i= 0,count=rows.length; i<count; i++) {
                var row = rows[i];
                listIds.push(row.list_id);
            }
            return listIds.join(',');
        },
        save:function(){
            var that = this;
            var url = '{$urlHrefs.sendCampaignAddLists}';
            var listsStr = that.getLists();
            url = GLOBAL.func.addUrlParam(url,'lists',listsStr);
            var schedulingParamStr = $('#sendCampaignAddSchedulingForm').serialize();
            url += '&';
            url += schedulingParamStr;
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(url, {}, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.tip('提示', res.msg, 'info');
                }
            }, 'json');
        }
    };
</script>