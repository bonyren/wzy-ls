<form id="subscribersImportCsvfileForm" method="post" enctype="multipart/form-data">
<table class="table-form">
    <tr>
        <td colspan="2">
            <p class="form-tip m-1">
                <span class="fa fa-info-circle"></span>
                您上传的文件必须是之前从CamelList系统导出的，不支持从其他系统导出的CSV文件。
            </p>
        </td>
    </tr>
    <tr>
        <td class="field-label" width="40%">包含邮件的CSV文件:</td>
        <td class="field-input">
            <input id="uploadEmailCsvFile" name="uploadEmailCsvFile" class="easyui-filebox" data-options="required:true,buttonIcon:'fa fa-file'" style="width:100%;" />
        </td>
    </tr>
    <tr>
        <td class="form-tip m-1" colspan="2">
            <span class="fa fa-info-circle"></span>
            如果您选择“覆盖已存在的”，在系统中已存在的订阅者信息将被导入文件的数据覆盖，“是否已存在”是通过订阅者的邮件地址来匹配。
        </td>
    </tr>
    <tr>
        <td class="field-label">覆盖已存在的:</td>
        <td class="field-input"><input type="checkbox" id="uploadEmailCsvFileOverwrite" name="uploadEmailCsvFileOverwrite" value="1"/></td>
    </tr>
</table>
<p class="bg-light m-1">
    <span class="fa fa-info-circle"></span>
    如果选择“测试输出”，您将看到上传文件解析后的邮件地址列表，这些邮件不会真正导入到系统，这被用来检测上传文件的格式是否正确。
</p>
<p class="text-center">
    <a id="testEmailCsvFilelBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-flask',
                            onClick:function(){
                                subscribersImportCsvfileModule.testOutput();
                            }">测试输出
    </a>
    <a id="uploadEmailCsvFilelBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-upload',
                            onClick:function(){
                                subscribersImportCsvfileModule.submit();
                            }">导入
    </a>
</p>
</form>
<script type="text/javascript">
    var subscribersImportCsvfileModule = {
        dialog:'#globel-dialog-div',
        form:'#subscribersImportCsvfileForm',
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
            var url = '{$urlHrefs.subscribersImportCsvfile}';
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
            var url = '{$urlHrefs.subscribersImportCsvfile}';
            url = GLOBAL.func.addUrlParam(url, 'lists', listsStr);
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
                    alert('onBeforeLoad');
                },
                onLoadSuccess:function(data){
                    //alert(data.code);
                    alert('onLoadSuccess');
                },
                onLoadError:function(){
                    alert('onLoadError');
                }
            });
        }
    };
</script>