<?php
use app\index\model\Admins as AdminsModel;
?>
<form>
    <table class="table-form">
        <tr>
            <td class="field-label" style="width: 30%;">登录名:</td>
            <td class="field-input"><input class="easyui-textbox" name="infos[login_name]" value="<?=$bindValues['infos']['login_name']?>"
                                           data-options="required:true,width:'100%',validType:['length[1,20]']"></td>
        </tr>
        <tr>
            <td class="field-label">姓名:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="infos[realname]" value="<?=$bindValues['infos']['realname']?>"
                    data-options="required:true,width:'100%',validType:['length[1,20]']">
            </td>
        </tr>
        <tr>
            <td class="field-label">Email:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="infos[email]" value="<?=$bindValues['infos']['email']?>"
                    data-options="required:true,width:'100%',validType:['length[1,60]', 'email', 'remote[\'{$urlHrefs.checkAdminEmail}\', \'email\']']">
            </td>
        </tr>
        <tr>
            <td class="field-label">超级管理员</td>
            <td class="field-input">
                <input id="adminSuperUserCheckbox" class="easyui-checkbox" name="infos[super_user]" value="<?=AdminsModel::eAdminSuperRole?>"
                       data-options="onChange:adminEditModule.onSuperUserChange,checked:<?php if($bindValues['infos']['super_user'] == AdminsModel::eAdminSuperRole){
                           echo "true";
                       }else{
                           echo "false";
                       }?>"/>
            </td>
        </tr>
        <tr id="adminRoleRow">
            <td class="field-label">角色</td>
            <td class="field-input">
                <select id="adminRoleCombobox" class="easyui-combobox" name="infos[role_id]" data-options="editable:true,limitToList:true,panelHeight:'auto',width:200">
                    <?php foreach($bindValues['adminRolePairs'] as $key=>$value){ ?>
                        <option value="<?=$key?>" <?=$bindValues['infos']['role_id']==$key?'selected':''?>><?=$value?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="field-label">帐号禁用</td>
            <td class="field-input">
                <input class="easyui-checkbox" name="infos[disabled]" value="<?=AdminsModel::eAdminDisabledStatus?>"
                    <?php if($bindValues['infos']['disabled'] == AdminsModel::eAdminDisabledStatus){
                        echo "checked";
                    }?>/>
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    var adminEditModule = {
        onSuperUserChange:function(checked){
            if(checked){
                $('#adminRoleRow').hide();
                $("#adminRoleCombobox").combobox('disableValidation');
            }else{
                $('#adminRoleRow').show();
                $("#adminRoleCombobox").combobox('enableValidation');
            }
        }
    };
    $.parser.onComplete = function(context){
        //console.log('onComplete');
        if($('#adminSuperUserCheckbox').length > 0) {
            if ($('#adminSuperUserCheckbox').checkbox('options').checked == true) {
                $('#adminRoleRow').hide();
            } else {
                $('#adminRoleRow').show();
            }
        }
    };
</script>