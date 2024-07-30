<form id="subscribersEditForm" style="height: 100%;">
    <table class="table-form mb-1">
        <tr>
            <td class="field-label" style="width:30%;">邮箱:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="email" value="{$bindValues.email}" data-options="required:true,width:'100%',
                validType:['length[1,128]', 'email', 'remote[\'{$urlHrefs.checkSubscriber}\', \'email\']']" />
            </td>
        </tr>
        <tr>
            <td class="field-label">姓名:</td>
            <td class="field-input">
                <input class="easyui-textbox" name="name" value="{$bindValues.name}" data-options="width:'100%',
                validType:['length[1,60]']" /></td>
        </tr>
    </table>
    <div class="easyui-tabs" data-options="fit:true,border:false">
        <div title="详情" data-options="iconCls:'fa fa-info', cache:true">
            <table class="table-form">
                <tr>
                    <td class="field-label" width="50%">接收HTML格式邮件:</td>
                    <td class="field-input">
                        <input class="easyui-checkbox" name="details[html_email]" value="1"
                            <?php
                            if($bindValues['details']['html_email'] == \app\index\Defs::eSubscriberHtmlEmail){
                                echo 'checked';
                            }
                            ?>
                        /></td>
                </tr>
            </table>
        </div>
        <div title="所属集合" data-options="iconCls:'fa fa-list', cache:false">
            <div id="listsDatalistToolbar">
                <input type="checkbox" id="checkAllListCheckbox" value="1"/>全部
            </div>
            <ul id="listsDatalist" class="easyui-datalist" data-options="checkbox:true,
                fit:true,
                lines:true,
                striped:true,
                singleSelect:false,
                border:false,
                toolbar:'#listsDatalistToolbar',
                onLoadSuccess:function(node,data){
                    editByDialogModule.init();
                }">
                {volist name="$bindValues.lists" id="list"}
                <li value="{$list.list_id}">
                    {$list.name}
                </li>
                {/volist}
            </ul>
        </div>
    </div>
</form>
<script type="text/javascript">
    var defaultCheckedLists = {$bindValues.checkedLists};
    $('#checkAllListCheckbox').change(function(){
        if(this.checked){
            $("#listsDatalist").datalist('checkAll');
        }else{
            $("#listsDatalist").datalist('uncheckAll');
        }
    });
    var editByDialogModule={
        init:function(){
            var rows = $("#listsDatalist").datalist('getRows');
            for(var i=0; i<rows.length; i++){
                var ids = rows[i].value.split('_');
                var listId= parseInt(ids[0]);
                var public = parseInt(ids[1]);
                if(-1 != $.inArray(Number(listId), defaultCheckedLists)){
                    $("#listsDatalist").datalist('checkRow', i);
                }
            }
        },
        serializeFormString:function(){
            var postStr = $("#subscribersEditForm").serialize();

            var listIds = [];
            var rows = $("#listsDatalist").datalist("getChecked");
            for(var i=0; i<rows.length; i++){
                var ids = rows[i].value.split('_');
                var listId= parseInt(ids[0]);
                var public = parseInt(ids[1]);
                listIds.push(listId);
            }
            postStr += "&lists=";
            postStr += encodeURIComponent(listIds.join(','));
            return postStr;
        }
    };
</script>