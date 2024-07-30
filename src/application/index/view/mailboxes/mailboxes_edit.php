<?php
use app\index\model\Mailboxes as MailboxesModel;
?>
<form>
    <table class="table-form">
        <tr>
            <td class="field-label">标题:</td>
            <td class="field-input"><input class="easyui-textbox" name="infos[title]" value="<?=$bindValues['infos']['title']?>"
                                           data-options="required:true,width:'100%',validType:['length[1,100]']" /></td>
        </tr>
        <!----------------------------------------------------------------------------------------------------------------->
        <tr><td colspan="2"></td></tr>
        <tr>
            <td class="field-label" style="width:40%;">发送姓名:</td>
            <td class="field-input"><input class="easyui-textbox" name="infos[from_name]" value="<?=$bindValues['infos']['from_name']?>"
                                           data-options="required:true,width:'100%',validType:['length[1,100]']" /></td>
        </tr>
        <tr>
            <td class="field-label">发送邮箱:</td>
            <td class="field-input"><input class="easyui-textbox" name="infos[from_email]" value="<?=$bindValues['infos']['from_email']?>"
                                           data-options="required:true,width:'100%',validType:['length[1,100]', 'email']" /></td>
        </tr>
        <!----------------------------------------------------------------------------------------------------------------->
        <tr><td colspan="2"></td></tr>
        <tr>
            <td class="field-label">发送服务器主机(SMTP):</td>
            <td class="field-input"><input class="easyui-textbox" id="smtp_host" name="infos[smtp_host]" value="<?=$bindValues['infos']['smtp_host']?>"
                                           data-options="required:true,width:'100%',validType:{length:[1,100]}" /></td>
        </tr>
        <tr>
            <td class="field-label">发送服务器端口(SMTP):</td>
            <td class="field-input"><input class="easyui-numberbox" id="smtp_port" name="infos[smtp_port]"
                                           value="<?=$bindValues['infos']['smtp_port']?>"
                                           data-options="required:true,
                                           width:'100%',
                                           max:65535,
                                           min:1,
                                           precision:0,
                                           validType:{length:[1,5]}" /></td>
        </tr>
        <tr>
            <td class="field-label">发送安全性:</td>
            <td class="field-input">
                <select id="smtp_secure" name="infos[smtp_secure]" class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" style="width:100%;">
                    <?php foreach(MailboxesModel::$eMailboxSecureDefs as $key=>$value){?>
                        <option value="<?=$key?>" <?php if($bindValues['infos']['smtp_secure'] == $key) echo "selected" ?>><?=$value?></option>
                    <?php }?>
                </select>
            </td>
        </tr>
        <!----------------------------------------------------------------------------------------------------------------->
        <tr><td colspan="2"></td></tr>
        <tr>
            <td class="field-label">接收服务器主机:</td>
            <td class="field-input"><input class="easyui-textbox" id="bounce_host" name="infos[bounce_host]" value="<?=$bindValues['infos']['bounce_host']?>"
                                           data-options="required:true,width:'100%',validType:{length:[1,100]}" /></td>
        </tr>
        <tr>
            <td class="field-label">接收服务器端口:</td>
            <td class="field-input"><input class="easyui-numberbox" id="bounce_port" name="infos[bounce_port]"
                                           value="<?=$bindValues['infos']['bounce_port']?>"
                                           data-options="required:true,
                                           width:'100%',
                                           max:65535,
                                           min:1,
                                           precision:0,
                                           validType:{length:[1,5]}" /></td>
        </tr>
        <tr>
            <td class="field-label">接收安全性:</td>
            <td class="field-input">
                <select id="bounce_secure" name="infos[bounce_secure]" class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" style="width:100%;">
                    <?php foreach(MailboxesModel::$eMailboxSecureDefs as $key=>$value){?>
                        <option value="<?=$key?>" <?php if($bindValues['infos']['bounce_secure'] == $key) echo "selected" ?>><?=$value?></option>
                    <?php }?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field-label">接收协议:</td>
            <td class="field-input">
                <select id="bounce_protocol" name="infos[bounce_protocol]" class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" style="width:100%;">
                    <?php foreach(MailboxesModel::$eMailboxProtocolDefs as $key=>$value){?>
                        <option value="<?=$key?>" <?php if($bindValues['infos']['bounce_protocol'] == $key) echo "selected" ?>><?=$value?></option>
                    <?php }?>
                </select>
            </td>
        </tr>
        <!-------------------------------------------------------------------------------------------------------------------->
        <tr><td colspan="2"></td></tr>
        <tr>
            <td class="field-label">账号:</td>
            <td class="field-input"><input class="easyui-textbox" id="account" name="infos[account]" value="<?=$bindValues['infos']['account']?>"
                                           data-options="required:true,width:'100%',validType:{length:[1,100]}" /></td>
        </tr>
        <tr>
            <td class="field-label">密码:</td>
            <td class="field-input"><input class="easyui-textbox" id="password" name="infos[password]" value="<?=$bindValues['infos']['password']?>"
                                           data-options="required:true,width:'100%',validType:{length:[1,100]}" /></td>
        </tr>
    </table>
</form>
<div class="text-center mt-1">
    <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ editMailboxesModule.testOutgoingServer(); },iconCls:'fa fa-flask'">测试发送服务器</a>
    <a href="#" class="easyui-linkbutton" data-options="onClick:function(){ editMailboxesModule.testIncomingServer(); },iconCls:'fa fa-flask'">测试接收服务器</a>
</div>
<script type="text/javascript">
    var editMailboxesModule = {
        testOutgoingServer:function(){
            var smtpHost = $("#smtp_host").textbox('getValue');
            var smtpPort = $("#smtp_port").textbox('getValue');
            var smtpSecure = $("#smtp_secure").combobox('getValue');
            var account = $("#account").textbox('getValue');
            var password = $("#password").textbox('getValue');

            var href = '{$urlHrefs.mailboxesTestOutgoingServer}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {
                smtp_host:smtpHost,
                smtp_port:smtpPort,
                smtp_secure:smtpSecure,
                account:account,
                password:password
            }, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.alert(null, '发送服务器正常');
                }
            }, 'json');
        },
        testIncomingServer:function(){
            var bounceHost = $("#bounce_host").textbox('getValue');
            var bouncePort = $("#bounce_port").textbox('getValue');
            var bounceSecure = $("#bounce_secure").combobox('getValue');
            var bounceProtocol = $("#bounce_protocol").combobox('getValue');
            var account = $("#account").textbox('getValue');
            var password = $("#password").textbox('getValue');

            var href = '{$urlHrefs.mailboxesTestIncomingServer}';
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {
                bounce_host:bounceHost,
                bounce_port:bouncePort,
                bounce_secure:bounceSecure,
                bounce_protocol:bounceProtocol,
                account:account,
                password:password
            }, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.alert(null, '接收服务器正常');
                }
            }, 'json');
        }
    };
</script>