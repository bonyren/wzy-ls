<?php
use app\index\Defs as IndexDefs;
?>
<form id="subscribersImportListForm" method="post">
    <p class="bg-light m-1">
        <span class="fa fa-info-circle"></span> 请在下面输入框中录入邮件地址，一行一个，然后点击"导入"按钮
    </p>
    <div>
        <input class="easyui-textbox" id="emailListContent" name="emailListContent"
               data-options="width:'100%',height:150,multiline:true,required:true" />
    </div>
    <p class="text-center p-2">
        <a id="emailListImportBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'fa fa-upload',onClick:function(){
                subscribersImportListModule.submit();
            }">导入
        </a>
    </p>
</form>

<script type="text/javascript">
    var subscribersImportListModule = {
        form: '#subscribersImportListForm',
        submit:function(){
            var that = this;
            var isValid = $(that.form).form('validate');
            if(!isValid){
                return;
            }
            var url = '{$urlHrefs.subscribersImportList}';
            $("#subscribersImportListForm").form('submit', {
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