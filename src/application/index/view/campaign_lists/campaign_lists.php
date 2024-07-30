<?php
use app\index\Defs as IndexDefs;
?>

<div id="campaign_lists_header" class="d-flex justify-content-around align-items-center">
    <div>
        <a href="#" class="easyui-linkbutton"
           data-options="onClick:function(){ campaignListsModule.create(); },iconCls:'fa fa-plus',size:'large'">
            创建投递活动
        </a>
    </div>
    <div class="buttongroup">
        <a href="javascript:;" class="easyui-linkbutton"
           data-options="onClick:function(){ campaignListsModule.loadCampaignList('<?=$urlHrefs['campaignDraftLists']?>'); },group:'campaign-lists-status-buttons',toggle:true,selected:true,iconCls:'fa fa-pencil',size:'large'">
            草稿
        </a>
        <a href="javascript:;" class="easyui-linkbutton"
           data-options="onClick:function(){ campaignListsModule.loadCampaignList('<?=$urlHrefs['campaignActiveLists']?>'); },group:'campaign-lists-status-buttons',toggle:true,selected:false,iconCls:'fa fa-check',size:'large'">
            已提交
        </a>
        <a href="javascript:;" class="easyui-linkbutton"
           data-options="onClick:function(){ campaignListsModule.loadCampaignList('<?=$urlHrefs['campaignSentLists']?>'); },group:'campaign-lists-status-buttons',toggle:true,selected:false,iconCls:'fa fa-history',size:'large'">
            已执行
        </a>
    </div>
</div>

<div id="campaignListsPanel" class="easyui-panel" data-options="border:false,
    fit:true,
    minimizable:false,
    maximizable:false,
    href:'{$urlHrefs.campaignDraftLists}',
    header:'#campaign_lists_header',
    onBeforeLoad:function(param){
        //alert('onBeforeLoad');
        if(campaignListsModule.countDownTimer){
            clearInterval(campaignListsModule.countDownTimer);
            campaignListsModule.countDownTimer=null;
        }
    },
    onDestroy:function(){
        //alert('onDestroy');
    }">
</div>
<script>
    var currentPanelUrl = '<?=$urlHrefs['campaignDraftLists']?>';
    var campaignListsModule = {
        dialog:'#globel-dialog-div',
        panel:'#campaignListsPanel',
        countDownTimer:null,
        loadCampaignList:function(url){
            if(currentPanelUrl != url) {
                $("#campaignListsPanel").panel('refresh', url);
                currentPanelUrl = url;
            }
        },
        create:function(){
            var that = this;
            $.post('{$urlHrefs.campaignListsCreate}', {}, function(res){
                if(!res.code){
                    $.app.method.alertError(null, res.msg, 'error');
                    return;
                }
                var campaignId = res.data;
                $(that.panel).panel('refresh');
                $.app.method.alert(null, "投递活动创建成功，请继续编辑。", function(){
                    var href = '{$urlHrefs.campaignListsEdit}';
                    href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
                    $(that.dialog).dialog({
                        title: '编辑新创建的投递活动',
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
                        onClose:function(){
                            $(that.panel).panel('refresh');
                        },
                        buttons:[{
                            text:'关闭',
                            iconCls:iconClsDefs.cancel,
                            handler: function(){
                                $(that.dialog).dialog('close');
                            }
                        }]
                    });
                    $(that.dialog).dialog('center');
                });
            }, 'json')
        },
        viewHyperlinkReport:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignHyperlinkStatis}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $(that.dialog).dialog({
                title: '查看超链接报告 - ' + subject,
                iconCls: 'fa fa-line-chart',
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
        formatCampaignStatus:function(val){
            return  <?=json_encode(IndexDefs::$eCampaignStatusHtmlDefs)?>[val];
        },
        view:function(campaignId, subject){
            var that = this;
            var href = '{$urlHrefs.campaignDetail}';
            href = GLOBAL.func.addUrlParam(href, 'campaignId', campaignId);
            $(that.dialog).dialog({
                title: '查看投递活动 - ' + subject,
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
                buttons:[],
                onClose:function(){
                }
            });
            $(that.dialog).dialog('center');
        }
    }
</script>