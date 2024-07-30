<div class="easyui-tabs" id="sendCampaignAddTabs" data-options="fit:true,border:false,
    onSelect:function(title, index){
    }">
    <!----------------------------------------------------------------------------------------------------->
    <div title="邮件内容" data-options="cache:true,
            iconCls:'icons-my-number1',
            href:'{$urlHrefs.sendCampaignAddContent}',
            onClose:function(){}">
    </div>
    <!----------------------------------------------------------------------------------------------------->
    <div title="邮件附件" data-options="cache:true,
            iconCls:'icons-my-number2',
            href:'{$urlHrefs.sendCampaignAddAttach}',
            onClose:function(){}">
    </div>
    <!----------------------------------------------------------------------------------------------------->
    <div title="目标与时间" data-options="cache:true,
            iconCls:'icons-my-number3',
            href:'{$urlHrefs.sendCampaignAddLists}',
            onClose:function(){}">
    </div>
    <!----------------------------------------------------------------------------------------------------->
    <div title="预览" data-options="cache:false,
            iconCls:'icons-my-number4',
            href:'{$urlHrefs.sendCampaignAddPreview}'">
    </div>
    <!----------------------------------------------------------------------------------------------------->
</div>
<script type="text/javascript">
    var sendCampaignAddModule = {
        dialog:'#globel-dialog-div',
        close:function(){
            $(sendCampaignAddModule.dialog).dialog('close');
        }
    };
</script>