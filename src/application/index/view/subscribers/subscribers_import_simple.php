<?php
use app\index\Defs as IndexDefs;
?>
<form id="subscribersImportSimpleForm" method="post">
    <table class="table-form">
        <tr>
            <td>
                <p class="form-tip m-1">
                    <span class="fa fa-info-circle"></span> 请在下面输入框中录入邮件地址，一行一个，然后点击"导入"按钮
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <input class="easyui-textbox" id="emailListContent" name="emailListContent" data-options="width:'100%',
                    height:200,
                    multiline:true,
                    required:true"/>
            </td>
        </tr>
    </table>
    <p class="text-center p-2">
        <a id="emailListImportBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-upload',onClick:function(){
            subscribersImportSimpleModule.submit();
            }">导入
        </a>
    </p>
</form>
<script type="text/javascript">
    var subscribersImportSimpleModule = {
        dialog:'#globel-dialog-div',
        form:'#subscribersImportSimpleForm',
        submit:function(){
            var that = this;
            var listsStr = subscribersImportModule.getImportLists();
            if(listsStr == ''){
                $.app.method.alertError(null, '请选择要导入的集合');
                return;
            }
            var url = '{$urlHrefs.subscribersImportSimple}';
            url += url.indexOf('?') != -1 ? '&lists=' + encodeURIComponent(listsStr) : '?lists=' + encodeURIComponent(listsStr);
            $(that.form).form('submit', {
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
                        if(obj.html){
                            $.app.method.alert('提示', obj.html, function(){
                                $(that.form).form('reset');
                            });
                        }else {
                            $.app.method.tip('提示', obj.msg, 'info');
                        }
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
</script>