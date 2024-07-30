<table id="campaignActiveListsDatagrid" class="easyui-datagrid" data-options="striped:true,
    nowrap:false,
    rownumbers:true,
    autoRowHeight:true,
    singleSelect:true,
    url:'{$urlHrefs.campaignLists}',
    method:'post',
    toolbar:'#campaignActiveListsToolbar',
    pagination:true,
    pageSize:<?=DEFAULT_PAGE_ROWS?>,
    pageList:[10,20,30,50,80,100],
    border:false,
    fit:true,
    fitColumns:<?=$loginMobile?'false':'true'?>,
    title:'',
    view: detailview,
    onLoadSuccess:function(row, data){
        campaignActiveListsModule.expandAll();
        campaignActiveListsModule.initEmbargoCountDown();
    },
    onClose:function(){
        console.log('campaignActiveListsModule - onClose');
    },
    onDestroy:function(){
        console.log('campaignActiveListsModule - onDestroy');
    },
    detailFormatter:function(index,row){
        return campaignActiveListsModule.detailFormatter();
    },
    onExpandRow:function(index,row){
        var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
        var href = '{$urlHrefs.campaignListsDetail}';
        var campaignId = row.campaign_id;
        href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
        ddv.panel({
            height:120,
            border:false,
            cache:false,
            href:href,
            onLoad:function(){
                $('#campaignActiveListsDatagrid').datagrid('fixDetailRowHeight',index);
            }
        });
        $('#campaignActiveListsDatagrid').datagrid('fixDetailRowHeight',index);
    }">
    <thead>
    <tr>
        <th data-options="field:'operate',width:150,align:'center',formatter:campaignActiveListsModule.operate">操作</th>
        <th data-options="field:'subject',width:200,align:'center'">主题</th>
        <th data-options="field:'entered',width:150,align:'center'">创建</th>
        <th data-options="field:'status',width:100,align:'center',formatter:campaignListsModule.formatCampaignStatus">状态</th>
        <th data-options="field:'embargo',width:150,align:'center',formatter:campaignActiveListsModule.formatEmbargo">投递倒计时</th>
    </tr>
    </thead>
</table>
<div id="campaignActiveListsToolbar" class="p-1">
    <form id="campaignActiveListsToolbarForm">
        主题: <input name="search[subject]" class="easyui-textbox"
                        data-options="width:200" />
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-search',
                    onClick:function(){ campaignActiveListsModule.search(); }">搜索
        </a>
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-reply',
                    onClick:function(){ campaignActiveListsModule.reset(); }">重置
        </a>
    </form>
</div>
<script>
    var campaignActiveListsModule = {
        dialog:'#globel-dialog-div',
        datagrid:'#campaignActiveListsDatagrid',
        searchform:'#campaignActiveListsToolbarForm',
        operate:function(val, row){
            var btns = [];
            if(row.status == <?=\app\index\Defs::eCampaignStatusSuspended?>) {
                btns.push('<a href="javascript:;" class="btn btn-outline-info size-MINI radius my-1" onclick="campaignActiveListsModule.requeue(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="重新投递"><i class="fa fa-play fa-lg">重新投递</i></a>');
                btns.push('<a href="javascript:;" class="btn btn-outline-dark size-MINI radius my-1" onclick="campaignActiveListsModule.markSent(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="投递完成"><i class="fa fa-send-o fa-lg">投递完成</i></a>');
                btns.push('<a href="javascript:;" class="btn btn-outline-primary size-MINI radius my-1" onclick="campaignActiveListsModule.edit(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="编辑"><i class="fa fa-pencil-square fa-lg">编辑</i></a>');
            }else{
                btns.push('<a href="javascript:;" class="btn btn-outline-danger size-MINI radius my-1" onclick="campaignActiveListsModule.suspend(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="中止"><i class="fa fa-pause fa-lg">中止</i></a>');
            }
            btns.push('<a href="javascript:;" class="btn btn-outline-success size-MINI radius my-1" onclick="campaignListsModule.view(' + row.campaign_id + ',\'' + GLOBAL.func.escapeALinkStringParam(row.subject) + '\')" title="查看"><i class="fa fa-eye fa-lg">查看</i></a>');
            return btns.join(' ');
        },
        formatEmbargo:function(val, row){
            return '<div id="count-down-' + row.campaign_id + '" class="btn-group"><span class="d label label-secondary"></span> <span class="label label-success"><span class="h"></span><span class="m"></span><span class="s"></span></span></div>';
        },
        initEmbargoCountDown:function(){
            var that = this;
            if(campaignListsModule.countDownTimer){
                clearInterval(campaignListsModule.countDownTimer);
                campaignListsModule.countDownTimer = null;
            }
            //如何结束？
            campaignListsModule.countDownTimer = setInterval(function(){
                if($('#campaignActiveListsDatagrid').length == 0){
                    //自动停止, 左边导航菜单切换的时候会触发
                    clearInterval(campaignListsModule.countDownTimer);
                    campaignListsModule.countDownTimer = null;
                    return;
                }
                //console.log('campaignListsModule.countDownTimer');
                var rows = $(that.datagrid).datagrid('getRows');
                for(var i= 0, len=rows.length; i<len; i++){
                    var campaignId = rows[i].campaign_id;
                    var countDownId = '#count-down-' + campaignId;
                    var beforeEmbargo = parseInt(rows[i].before_embargo);
                    var $dateShow = $(countDownId);
                    if($dateShow.length == 0){
                        return;
                    }
                    if(beforeEmbargo < 0){
                        $dateShow.find(".d").html('0 天');
                        $dateShow.find(".h").html('00:');
                        $dateShow.find(".m").html('00:');
                        $dateShow.find(".s").html('00');
                    }else{
                        var days = Math.floor(beforeEmbargo/(60*60*24));
                        var hours = Math.floor((beforeEmbargo-days*24*60*60)/3600);
                        var minutes = Math.floor((beforeEmbargo-days*24*60*60-hours*3600)/60);
                        var seconds = Math.floor(beforeEmbargo-days*24*60*60-hours*3600-minutes*60);
                        $dateShow.find(".d").html(days + ' 天');
                        $dateShow.find(".h").html(sprintf('%02d', [hours]) + ':');
                        $dateShow.find(".m").html(sprintf('%02d', [minutes]) + ':');
                        $dateShow.find(".s").html(sprintf('%02d', [seconds]));
                        rows[i].before_embargo -= 1;
                    }
                }
            }, 1000);
        },
        reload:function(){
            $(this.datagrid).datagrid('reload');
        },
        detailFormatter:function(){
            return '<div class="ddv" style="overflow: auto;"></div>';
        },
        expandAll:function(){
            var that = this;
            var rows = $(that.datagrid).datagrid('getRows');
            for(var i= 0,count=rows.length; i<count; i++){
                var row = rows[i];
                var index = $(that.datagrid).datagrid('getRowIndex', row);
                $(that.datagrid).datagrid('expandRow', index);
            }
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
            $(that.datagrid).datagrid('load');
        },
        reset:function(){
            var that = this;
            $(that.searchform).form('reset');
            var queryParams = $(that.datagrid).datagrid('options').queryParams;
            $.each($(that.searchform).serializeArray(), function() {
                delete queryParams[this['name']];
            });
            $(that.datagrid).datagrid('load');
        },
        requeue:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignRequeue}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $.messager.confirm('提示', `确认重新开始投递活动"${subject}"吗?`, function(result){
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
        suspend:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignSuspend}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $.messager.confirm('提示', `确认中止投递活动"${subject}"吗?`, function(result){
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
        markSent:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignMarkSent}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $.messager.confirm('提示', `确认完成投递活动"${subject}"吗?`, function(result){
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
        edit:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.sendCampaignEdit}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $(that.dialog).dialog({
                title: '修改投递活动 - ' + subject,
                iconCls: 'fa fa-pencil-square',
                width: '90%',
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
        }
    };

</script>