<?php
use app\index\Defs as IndexDefs;
?>
<div class="easyui-panel" data-options="fit:true,border:false">
    <header>
        <div class="d-flex justify-content-end align-items-center">
            <div class="mr-5">
                <a class="easyui-linkbutton" href="#" data-options="size:'large',iconCls:'fa fa-plus',onClick:function(){
                        sendCampaignAddPreviewModule.addToProcessQueue();
                    }">
                    加入投递队列
                </a>
            </div>
        </div>
    </header>
    <div style="height: 85%;">
        <iframe width="99%" height="100%" src="{$urlHrefs.previewCampaign}" frameborder="1"></iframe>
    </div>
    <div class="m-2" style="height: 10%">
        <a id="sendTestBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-flask', onClick:function(){
                sendCampaignAddPreviewModule.test();
                }">发送测试邮件
        </a>
        到
        <input class="easyui-textbox" id="test_emails" name="test_emails" value="" data-options="width:'50%',prompt:'用逗号,隔开多个订阅者邮箱'"/>
    </div>
</div>
<script type="text/javascript">
    var sendCampaignAddPreviewModule = {
        addToProcessQueue:function(){
            var url = '{$urlHrefs.addCampaignProcessQueue}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(url, {}, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.tip('提示', res.msg, 'info');
                    //close the dialog
                    sendCampaignAddModule.close();
                }
            }, 'json');
            return false;
        },
        test:function(){
            var testEmails = $("#test_emails").val();
            var href = '{$urlHrefs.sendCampaignTest}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {test_emails:testEmails}, function(res){
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