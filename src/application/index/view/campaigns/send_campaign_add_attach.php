<?php
use app\index\Defs as IndexDefs;
?>
<div class="easyui-panel" data-options="fit:true,border:false">
    <header>
        <div class="d-flex justify-content-end align-items-center">
            <div class="mr-5">
                <a class="easyui-linkbutton" href="#"  data-options="size:'large',iconCls:'fa fa-save',
                            onClick:function(){
                                sendCampaignAddAttachModule.save();
                            }">保存
                </a>
            </div>
        </div>
    </header>
    <form id="sendCampaignAddAttachForm" method="post">
        <table class="table-form">
            <tr>
                <td class="p-1">
                    <a class="easyui-linkbutton" data-options="iconCls:'fa fa-cloud-upload',onClick:function(){
                        sendCampaignAddAttachModule.uploadAttach();
                    }">上传文件
                    </a>
                </td>
            </tr>
            <tr>
                <td height="200px">
                    <div id="campaignAttaches">
                        <?php foreach($bindValues['attaches'] as $attach){ ?>
                            <div id="campaign_attach_<?=$attach['campaign_attach_id']?>" class="attach-box">
                                <div class="text-center mt-1">
                                    <a title="<?=$attach['original_name']?>" href="javascript:void(0)" onclick="sendCampaignAddAttachModule.previewAttach(<?=$attach['campaign_attach_id']?>)">
                                        <img class="avatar size-XXL" src="<?=$attach['thumbnail_url']?>" />
                                    </a>
                                </div>
                                <div class="text-center attach-name">
                                    <?=$attach['original_name']?>
                                </div>
                                <div class="text-center attach-size">
                                    <?=number_format($attach['size'])?> Bytes
                                </div>
                                <div class="text-center mt-1 attach-buttons">
                                    <a class="btn btn-outline-danger size-MINI fa fa-remove" href="javascript:void(0)" onclick="sendCampaignAddAttachModule.deleteAttach(<?=$attach['campaign_attach_id']?>)">&nbsp;</a>
                                    <a class="btn btn-outline-secondary size-MINI fa fa-download" href="<?=$attach['download_url']?>" target="_blank">&nbsp;</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
    var sendCampaignAddAttachModule = {
        dialog:'#globel-dialog-div',
        dialog2:'#globel-dialog2-div',
        uploadAttach:function(){
            var url = '{$urlHrefs.uploadCampaignAttach}';
            $.app.method.uploadOne(url, function (obj) {
                if(!obj.code){
                    $.app.method.tip('提示',obj.msg,'error');
                    return;
                }else{
                    $.app.method.tip('提示','success','info');
                }
                var html = '<div id="campaign_attach_' + obj.data.campaign_attach_id + '" class="attach-box">' +
                        '<div class="text-center mt-1"><a title="' + obj.data.name + '" href="javascript:void(0)" onclick="sendCampaignAddAttachModule.previewAttach('+obj.data.campaign_attach_id+')">'+
                        '<img class="avatar size-XXL" src="' + obj.data.thumbnail_url + '" /></a></div>' +
                        '<div class="text-center attach-name">' + obj.data.name + '</div>' +
                        '<div class="text-center attach-size">' + obj.data.size + ' Bytes</div>' +
                        '<div class="text-center mt-1 attach-buttons">' +
                        '<a class="btn btn-outline-danger size-MINI fa fa-remove" href="javascript:void(0)" onclick="sendCampaignAddAttachModule.deleteAttach('+obj.data.campaign_attach_id+')">&nbsp;</a>'+
                        ' ' +
                        '<a class="btn btn-outline-secondary size-MINI fa fa-download" href="' + obj.data.download_url + '" target="_blank">&nbsp;</a></div>' +
                    '</div>';
                $("#campaignAttaches").append(html);
            });
        },
        previewAttach:function(campaignAttachId){
            var that = this;
            var href = '{$urlHrefs.previewCampaignAttach}';
            href += href.indexOf('?') != -1 ? '&campaignAttachId=' + campaignAttachId : '?campaignAttachId='+campaignAttachId;
            $(that.dialog2).dialog({
                title: '预览附件',
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
                        $(that.dialog2).dialog('close');
                    }
                }]
            });
            $(that.dialog2).dialog('center');
        },
        deleteAttach:function(campaignAttachId){
            var that = this;
            var href = '{$urlHrefs.deleteCampaignAttach}';
            href += href.indexOf('?') != -1 ? '&campaignAttachId=' + campaignAttachId : '?campaignAttachId='+campaignAttachId;
            $.messager.confirm('提示', '确定删除吗?', function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {}, function(res){
                    $.messager.progress('close');
                    if(!res.code){
                        $.app.method.alertError(null, res.msg);
                    }else{
                        $.app.method.tip('提示', res.msg, 'info');
                        $('#campaign_attach_' + campaignAttachId).remove();
                    }
                }, 'json');
            });
        },
        downloadAttach:function(campaignAttachId){
            alert('downloadAttach');
        },
        save:function(){
            var url = '{$urlHrefs.sendCampaignAddAttach}';
            $("#sendCampaignAddAttachForm").form('submit',{
                url:url,
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
        }
    }
</script>