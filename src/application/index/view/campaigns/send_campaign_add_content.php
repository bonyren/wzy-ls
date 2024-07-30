<?php
use app\index\Defs as IndexDefs;
?>
<style>
    .campaign-message-textarea {
        width: 400px;
        min-height: 26px;
        line-height: 20px;
        _height: 30px;
        /* max-height: 150px;*/
        margin-left: auto;
        margin-right: auto;
        padding: 3px;
        outline: 0;
        border: 1px solid #ccc;
        font-size: 12px;
        word-wrap: break-word;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-user-modify: read-write-plaintext-only;
        border-radius: 4px;
    }
</style>

<div class="easyui-panel" data-options="fit:true,border:false">
    <header>
        <div class="d-flex justify-content-end align-items-center">
            <div class="mr-5">
                <a class="easyui-linkbutton" href="#"  data-options="size:'large',iconCls:'fa fa-save',
                            onClick:function(){
                                var isValid = $('#sendCampaignAddContentForm').form('validate');
                                if(!isValid) return;
                                sendCampaignAddContentModule.save();
                            }">保存
                </a>
            </div>
        </div>
    </header>
    <form id="sendCampaignAddContentForm" method="post" style="height: 90%;">
        <table class="table-form" style="height: 100%">
            <tr style="height:30px;">
                <td class="field-label" style="width: 100px;"><label>主题:</label></td>
                <td class="field-input" colspan="5">
                    <input class="easyui-textbox" name="subject" value="{$bindValues.subject}"
                           data-options="required:true,width:'100%',prompt:'无主题',validType:['length[1,255]']"/>
                </td>
            </tr>
            <tr style="height:30px;">
                <td class="field-label"><label>内容格式:</label></td>
                <td class="field-input">
                    <select name="send_format" class="easyui-combobox" data-options="required:true,editable:false,panelHeight:'auto',value:'<?=$bindValues['send_format']?>'" style="width:200px;">
                        <?php
                        foreach(IndexDefs::$eCampaignSendFormatDefs as $key=>$value) {
                            ?>
                            <option value="<?=$key?>"><?=$value?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td class="field-label" style="width: 100px;"><label>内容模板:</label></td>
                <td class="field-input">
                    <!--combobox required:true情况下的正确做法-->
                    <select id="templatesCombobox" name="template_id" class="easyui-combobox"
                            data-options="required:true,editable:false,value:'<?=$bindValues['template_id']?>'" style="width:200px;">
                        <option value="0">--不使用模板--</option>
                        <?php foreach($bindValues['templates'] as $template){ ?>
                            <option value="<?=$template['template_id']?>"><?=$template['title']?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="field-label" style="width: 100px;"><label>发送邮箱:</label></td>
                <td class="field-input">
                    <select class="easyui-combobox" name="mailbox_id" data-options="required:true,editable:false,value:'<?=$bindValues['mailbox_id']?>'" style="width:200px;">
                        <?php if($bindValues['mailbox_id'] == 0){ ?>
                            <option value="0"></option>
                        <?php } ?>
                        <?php foreach($bindValues['mailboxes'] as $mailbox){ ?>
                            <option value="<?=$mailbox['mailbox_id']?>"><?=$mailbox['title']?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="field-label"><label>内容:</label></td>
                <td class="field-input" id="contentEditorContainer" colspan="5">
                <textarea id="campaign-message"
                          name="message_content"
                          rows="10"
                          cols="80"
                          class="ckeditor campaign-message-textarea"
                    >
                    {$bindValues.message_content}
                </textarea>
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">
    var sendCampaignAddContentModule = {
        init:function(){
            CKEDITOR.replace('campaign-message', {
                filebrowserImageUploadUrl : '{$urlHrefs.uploadImage}',
                height: 100,
                width: '100%',
                uiColor: '#f5f5f5',
                toolbar: 'Basic',
                allowedContent: true,
                on : {
                    // maximize the editor on startup
                    'instanceReady' : function( evt ) {
                        evt.editor.resize("100%", $("#contentEditorContainer").height());
                    }
                }
            });
        },
        save:function(){
            var url = '{$urlHrefs.sendCampaignAddContent}';
            $("#sendCampaignAddContentForm").form('submit',{
                url:url,
                onSubmit:function(param){
                    var isValid = $(this).form('validate');
                    if (!isValid) return false;
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
    };
    sendCampaignAddContentModule.init();
</script>