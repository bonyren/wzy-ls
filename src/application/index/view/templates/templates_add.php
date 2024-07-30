<form style="height: 98%;">
    <table class="table-form" style="height: 100%;">
        <tr>
            <td class="field-label" style="width: 10%;height: 5%;">标题:</td>
            <td class="field-input">
                <input id="campaignTemplateTitle" class="easyui-textbox" name="title" value="{$bindValues.title}" data-options="required:true,width:'100%',
                    validType:['length[1,255]']" />
            </td>
        </tr>
        <tr>
            <td class="field-label" style="height: 75%;">内容:</td>
            <td class="field-input" id="templateEditorContainer">
                <textarea id="campaignTemplateContent" name="template" rows="20" cols="80" class="ckeditor">{$bindValues.template}</textarea>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    CKEDITOR.replace('campaignTemplateContent', {
        filebrowserImageUploadUrl : '{$urlHrefs.uploadImage}',
        width: '100%',
        uiColor: '#f1e4db',
        toolbar: 'Basic',
        allowedContent: true,
        on : {
            // maximize the editor on startup
            'instanceReady' : function( evt ) {
                evt.editor.resize("100%", $("#templateEditorContainer").height());
            }
        }
    });
    $(window).resize(function() {
        CKEDITOR.instances['campaignTemplateContent'].resize("100%", $("#templateEditorContainer").height());
    });

    var templatesAddModule = {
        serializeForm:function(){
            var title = $("#campaignTemplateTitle").val();
            var template = CKEDITOR.instances.campaignTemplateContent.getData();
            return 'title=' + encodeURIComponent(title) + "&template=" + encodeURIComponent(template);
        }
    };
</script>