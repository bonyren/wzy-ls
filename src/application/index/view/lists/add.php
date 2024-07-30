<form>
    <table class="table-form">
        <tr>
            <td class="field-label" style="width: 30%;">名称:</td>
            <td class="field-input"><input class="easyui-textbox" name="formData[name]" data-options="required:true,width:'100%',
            validType:['length[1,60]']" /></td>
        </tr>
        <tr>
            <td class="field-label">描述:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="formData[description]"
                       data-options="required:true,multiline:true,width:'100%',height:100,validType:['length[1,255]']" />
            </td>
        </tr>
    </table>
</form>