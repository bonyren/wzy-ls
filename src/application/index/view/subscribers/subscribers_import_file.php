<form id="subscribersImportFileForm" method="post" enctype="multipart/form-data">
<table class="table-form">
    <tr>
        <td colspan="2">
            <p class="form-tip m-1">
                <span class="fa fa-info-circle"></span>
                您上传的文件将需要包含要添加到这些集合的电子邮件。 一行包含一封电子邮件。 电子邮件之后的所有内容都将被忽略。文件必须是纯文本格式，不能上传类似于word这样的二进制文件。
            </p>
        </td>
    </tr>
    <tr>
        <td class="field-label" width="30%">包含邮件的文件:</td>
        <td class="field-input"><input id="uploadEmailFile" name="uploadEmailFile" class="easyui-filebox" data-options="required:true, buttonIcon:'fa fa-file'" style="width:100%;" /></td>
    </tr>
</table>

<p class="bg-light m-1">
    <span class="fa fa-info-circle"></span>
    如果选择“测试输出”，您将看到上传文件解析后的邮件地址列表，这些邮件不会真正导入到系统，这被用来检测上传文件的格式是否正确。
</p>
<p class="text-center">
    <a id="testEmailFilelBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-flask',
                            onClick:function(){
                                subscribersImportFileModule.testOutput();
                            }">测试输出
    </a>
    <a id="uploadEmailFilelBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-upload',
                            onClick:function(){
                                subscribersImportFileModule.submit();
                            }">导入
    </a>
</p>
</form>
<script type="text/javascript">
    var subscribersImportFileModule = {
        dialog:'#globel-dialog-div',
        form: '#subscribersImportFileForm',
        testOutput:function(){
            var that = this;
            var listsStr = subscribersImportModule.getImportLists();
            if(listsStr == ''){
                $.app.method.alertError(null, '请选择要导入的集合');
                return;
            }
            var isValid = $(that.form).form('validate');
            if(!isValid){
                return;
            }
            var url = '{$urlHrefs.subscribersImportFile}';
            url = GLOBAL.func.addUrlParam(url, 'lists', listsStr);
            url = GLOBAL.func.addUrlParam(url, 'test', 1);
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
                                //$(that.form).form('reset');
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
        },
        submit:function(){
            var that = this;
            var listsStr = subscribersImportModule.getImportLists();
            if(listsStr == ''){
                $.app.method.alertError(null, '请选择要导入的集合');
                return;
            }
            var isValid = $(that.form).form('validate');
            if(!isValid){
                return;
            }
            var url = '{$urlHrefs.subscribersImportFile}';
            url = GLOBAL.func.addUrlParam(url, 'lists', listsStr);
            $(that.form).form('submit', {
                url:url,
                onSubmit:function(param){
                    $.messager.progress({text:'处理中，请稍候...'});
                    /*
                    var $bar = $.messager.progress('bar');
                    setInterval(function() {
                        var rand = GLOBAL.func.random(0, 100);
                        $bar.progressbar({text: 'hello world - ' + rand, value: rand});
                    }, 2000);*/
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