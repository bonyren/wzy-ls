<div class="daohe-panel daohe-panel-default" style="border-color:#fff;">
    <div class="daohe-panel-header clearfix">
        <div class="pull-left">
        <a class="easyui-linkbutton" data-options="iconCls:'fa fa-cloud-upload',onClick:function(){
                                attachModule_{$uniqid}.uploadAttach();
                            }">上传</a>
        </div>
        <div class="pull-right">
            <?php if(!empty($prompt)){ ?>
                <span class="fa fa-info-circle"><?=$prompt?></span>
            <?php } ?>
        </div>
    </div>
    <div class="daohe-panel-body">
        <div id="attaches_{$uniqid}" class="clearfix">
            <?php foreach($bindValues['attaches'] as $attach){ ?>
                <div id="attach_file_{$uniqid}_<?=$attach['attachment_id']?>" class="attach-box">
                    <div class="text-center">
                        <a title="<?=$attach['original_name']?>" href="javascript:void(0)" onclick="QT.filePreview(<?=$attach['attachment_id']?>)">
                            <img class="avatar size-XXL" src="<?=$attach['thumbnail_url']?>" />
                        </a>
                    </div>
                    <div class="text-center attach-name">
                        <?=$attach['original_name']?>
                    </div>
                    <div class="text-center attach-size">
                        <?=number_format($attach['size'])?> Bytes
                    </div>
                    <div class="text-center attach-buttons">
                        <a class="btn btn-outline-danger size-MINI fa fa-remove" href="javascript:void(0)" onclick="attachModule_{$uniqid}.deleteAttach(<?=$attach['attachment_id']?>)">&nbsp;</a>
                        <a class="btn btn-outline-secondary size-MINI fa fa-download" href="<?=$attach['download_url']?>" target="_blank">&nbsp;</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var attachModule_{$uniqid} = {
        dialog:'#globel-dialog-div',
        dialog2:'#globel-dialog2-div',
        callback:<?=$bindValues['callback'] ? $bindValues['callback'] : 'null'?>,
        queries:<?=json_encode($_GET,JSON_UNESCAPED_UNICODE)?>,
        uploadAttach:function(){
            var that = this;
            var url = '{$urlHrefs.uploadAttach}';
            $.app.method.upload(url, function (obj) {
                if(!obj.code){
                    $.app.method.tip('提示',obj.msg,'error');
                    return;
                }else{
                    if (obj.html) {
                        $.messager.alert('提示',obj.html,'warning');
                    } else {
                        $.app.method.tip('提示','成功','info');
                    }
                }
                var html = '';
                $.each(obj.data,function(i,v){
                    html += '<div id="attach_file_{$uniqid}_' +v.attachment_id + '" class="attach-box">' +
                        '<div class="text-center"><a title="' + v.name + '" href="javascript:void(0)" onclick="QT.filePreview('+v.attachment_id+')">'+
                        '<img class="avatar size-XXL" src="' + v.thumbnail_url + '" /></a></div>' +
                        '<div class="text-center attach-name">' + v.name + '</div>' +
                        '<div class="text-center attach-size">' + v.size + ' Bytes</div>' +
                        '<div class="text-center attach-buttons">' +
                        '<a class="btn btn-outline-danger size-MINI fa fa-remove" href="javascript:void(0)" onclick="attachModule_{$uniqid}.deleteAttach('+v.attachment_id+')">&nbsp;</a>'+
                        ' ' +
                        '<a class="btn btn-outline-secondary size-MINI fa fa-download" href="' + v.download_url + '" target="_blank">&nbsp;</a></div>' +
                        '</div>';
                });
                $("#attaches_{$uniqid}").append(html);
                if (that.callback) {
                    that.callback(obj.data,that.queries);
                }
            });
        },
        deleteAttach:function(attachmentId){
            var that = this;
            var href = '{$urlHrefs.deleteAttach}';
            href += href.indexOf('?') != -1 ? '&attachmentId=' + attachmentId : '?attachmentId='+attachmentId;
            $.messager.confirm('提示', '确认删除吗?', function(result){
                if(!result) return false;
                $.messager.progress({text:'处理中，请稍候...'});
                $.post(href, {}, function(res){
                    $.messager.progress('close');
                    if(!res.code){
                        $.app.method.alertError(null, res.msg);
                    }else{
                        $.app.method.tip('提示', res.msg, 'info');
                        $('#attach_file_{$uniqid}' + attachmentId).remove();
                    }
                }, 'json');
            });
        }
    }
</script>