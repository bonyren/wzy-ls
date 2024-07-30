<form method="post" style="width: 100%;height: 100%;">
    <table class="table-form">
        <tr>
            <td class="field-label">名称:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="infos[name]"
                       data-options="required:true,width:360,validType:['length[1,32]']"
                       value="<?=$infos['name']?>"/>
            </td>
        </tr>
    </table>
</form>