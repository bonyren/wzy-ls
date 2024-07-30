<?php
use app\index\Defs as IndexDefs;
?>

<div id="bounce-status-buttons" class="d-flex justify-content-around align-items-center">
    <div class="buttongroup">
        <a href="javascript:;" class="easyui-linkbutton"
           data-options="onClick:function(){ bouncesModule.loadBounceList('<?=$urlHrefs['bounceProcessedLists']?>', <?=IndexDefs::eBouncePending?>); },group:'bounce-status-buttons',toggle:true,selected:true,iconCls:'fa fa-check',size:'large'">
            已处理
        </a>
        <a href="javascript:;" class="easyui-linkbutton"
           data-options="onClick:function(){ bouncesModule.loadBounceList('<?=$urlHrefs['bounceUnidentifiedLists']?>', <?=IndexDefs::eBounceUnidentified?>); },group:'bounce-status-buttons',toggle:true,selected:false,iconCls:'fa fa-eye-slash',size:'large'">
            未识别
        </a>
    </div>
</div>
<div class="easyui-panel" data-options="border:false,fit:true,header:'#bounce-status-buttons'">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'north',collapsible:false,border:false" style="height:80%;">
            <table id="bouncesDatagrid" class="easyui-datagrid" data-options="striped:true,
                nowrap:false,
                rownumbers:true,
                autoRowHeight:true,
                singleSelect:true,
                checkOnSelect:false,
                selectOnCheck:false,
                url:'{$urlHrefs.bounceProcessedLists}',
                method:'post',
                toolbar:'#bouncesToolbar',
                pagination:true,
                pageSize:<?=DEFAULT_PAGE_ROWS?>,
                pageList:[10,20,30,50,80,100],
                border:false,
                fit:true,
                fitColumns:<?=$loginMobile?'false':'true'?>,
                title:'',
                onLoadSuccess:function(row, data){
                },
                ">
                <thead>
                <tr>
                    <th data-options="field:'ck',checkbox:true"></th>
                    <th data-options="field:'operate',width:120,align:'center',formatter:bouncesModule.operate">操作</th>
                    <th data-options="field:'campaign_subject',width:200,align:'center'">投递活动</th>
                    <th data-options="field:'subscriber_email',width:200,align:'center'">订阅者</th>
                    <th data-options="field:'date',width:200,align:'center'">日期</th>
                </tr>
                </thead>
            </table>
            <div id="bouncesToolbar"  class="p-1">
                <form id="bouncesToolbarForm">
                    邮箱: <select class="easyui-combobox" name="search[mailbox]" data-options="editable:false, panelHeight:'auto', url:'{$urlHrefs.mailboxsComboboxDatas}',
                        valueField: 'mailbox_id',
                        textField: 'title'" style="width: 200px">
                    </select>
                    主题: <input name="search[subject]" class="easyui-textbox"
                                    data-options="width:200" />
                    <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                                onClick:function(){ bouncesModule.search(); }">搜索
                    </a>
                    <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                                onClick:function(){ bouncesModule.reset(); }">重置
                    </a>
                </form>
            </div>
        </div>

        <div data-options="region:'center',border:true">
            <form id="bouncesActionForm" method="post" style="width: 100%;height: 100%">
                <table class="table-form">
                    <tr>
                        <td class="field-label">操作目标</td>
                        <td class="field-input" colspan="2">
                            <div class="m-1">
                                <input id="actionTargetTagsRadio" class="easyui-radiobutton" name="bouncesTarget" value="<?=IndexDefs::eBounceActionTargetTags?>" checked/>
                                <?=IndexDefs::$eBounceActionTargetDefs[IndexDefs::eBounceActionTargetTags]?>

                                <input type="radio" class="easyui-radiobutton" name="bouncesTarget" value="<?=IndexDefs::eBounceActionTargetAll?>" />
                                <?=IndexDefs::$eBounceActionTargetDefs[IndexDefs::eBounceActionTargetAll]?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-label">
                            操作动作
                        </td>
                        <td class="field-input">
                            <div class="m-1">
                                <select name="bouncesAction" class="easyui-combobox" editable="false" panelHeight="auto" style="width:400px">
                                    <?php foreach(IndexDefs::$eBounceRegexActionDefs as $key=>$value){
                                        echo '<option value="' . $key . '">' . $value . '</option>';
                                    }?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <a id="bouncesActionBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-gavel',onClick:function(){bouncesModule.action();}">执行</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
    var currentPanelUrl = '<?=$urlHrefs['bounceProcessedLists']?>';
    var currentProcessedStatus = <?=IndexDefs::eBouncePending?>;
    var bouncesModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#bouncesDatagrid',
        searchform:'#bouncesToolbarForm',
        loadBounceList:function(url, status){
            currentProcessedStatus = status;
            if(currentPanelUrl != url) {
                $("#bouncesDatagrid").datagrid('load', url);
                currentPanelUrl = url;
            }
        },
        operate:function(val, row){
            var btns = [];
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius my-1" onclick="bouncesModule.view(' + row.bounce_id + ')" title="查看"><i class="fa fa-eye fa-lg">查看</i></a>');
            return btns.join(' ');
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        load:function(){
            $(this.datagrid).datagrid('load');
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
            that.load();
        },
        reset:function(){
            var that = this;
            $(that.searchform).form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($(that.searchform).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            that.load();
        },
        view:function(bounceId){
            var that = this;
            var href = '{$urlHrefs.bounceDetail}';
            href = GLOBAL.func.addUrlParam(href, 'bounceId', bounceId);
            $(that.dialog).dialog({
                title: '查看退信',
                iconCls: 'fa fa-eye',
                width: '80%',
                height: '100%',
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
                    }
                }]
            });
            $(that.dialog).dialog('center');
        },
        action:function(){
            var that = this;
            var checkedBounceIds = [];
            var checkedRows = $(that.datagrid).datagrid('getChecked');
            for(var i= 0,count=checkedRows.length; i<count; i++){
                checkedBounceIds.push(checkedRows[i].bounce_id);
            }
            var targetTags = $('#actionTargetTagsRadio').radiobutton('options').checked;
            if(targetTags && checkedBounceIds.length == 0){
                $.app.method.alertError(null, '请选择要操作的退信');
                return;
            }

            var url = '{$urlHrefs.bouncesAction}';
            ////////////////////////////////////////////////////////////
            var queryParams = {};
            //状态
            queryParams.bounceProcessedStatus = currentProcessedStatus;
            //查询参数
            $.each($(that.searchform).serializeArray(), function() {
                queryParams[this['name']] = this['value'];
            });
            //操作目标
            queryParams.bounceIds = checkedBounceIds.join(',');
            /////////////////////////////////////////////////////////////
            $.messager.confirm('提示', '确认执行该操作吗？', function(result) {
                if (!result) return false;
                $("#bouncesActionForm").form('submit', {
                    url:url,
                    queryParams:queryParams,
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
                    },
                    onBeforeLoad:function(param){
                        //alert('onBeforeLoad');
                    },
                    onLoadSuccess:function(data){
                        //alert('onLoadSuccess');
                    },
                    onLoadError:function(){
                        //alert('onLoadError');
                    }
                });
            });
        }
    };

</script>
</div>