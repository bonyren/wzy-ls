<?php
use app\index\Defs as IndexDefs;
?>
<div class="easyui-layout" data-options="fit:true">
    <div id="importListsDestination" data-options="region:'north',
                                                    collapsible:false,
                                                    title:'第1步: 选择要导入的目标集合'" style="height:38%;">
        <div id="listsAllTabs" class="easyui-tabs" data-options="fit:true,border:false">
            <!----------------------------------------------------------------------------------------------------->
            <div title="@" data-options="id:'0',cache:false" class="ml-10 mt-10" id="allListTab">
                <?php
                foreach(IndexDefs::$eSubscriberImportListsDefs as $key=>$text){
                    echo '<div class="m-2"><input id="allListCheckbox_' . $key .'" name="allListCheckbox[]" type="checkbox" value="' . $key. '"/>' . $text . '</div>';
                } ?>
            </div>
            <!----------------------------------------------------------------------------------------------------->
            <div title="目标集合" data-options="id:'1', iconCls:'fa fa-list-ul', cache:false">
                <ul id="listsDatalist_1" class="easyui-datalist" data-options="checkbox:true,
                    fit:true,
                    lines:true,
                    striped:true,
                    singleSelect:false,
                    border:false,
                    onLoadSuccess:function(node,data){
                    }">
                    {volist name="$bindValues.lists" id="list"}
                    <li value="{$list.list_id}">
                        <i style="display: inline-block;width: 300px">
                            {$list.name}
                        </i>
                         <i style="display: inline-block;width: 100px">
                            <span class="badge badge-secondary" title="订阅者数量">{$list.subscriber_count}</span>
                            <span class="badge badge-default text-deleted" title="黑名单订阅者数量">{$list.subscriber_blacklisted_count}</span>
                         </i>
                    </li>
                    {/volist}
                </ul>
            </div>
            <!----------------------------------------------------------------------------------------------------->
        </div>
    </div>
    <div id="importListsContent" data-options="region:'center',title:'第2步: 选择导入方式'">
        <div id="importListsTabs" class="easyui-tabs" data-options="fit:true,
            border:false,
            onSelect:function(title, index){
                var tabPanel = $('#importListsTabs').tabs('getTab', index);
                $('#importListsTabs').tabs('update', {
                    tab:tabPanel,
                    type:'header',
                    options:{
                    }
                });
            }">
            <!----------------------------------------------------------------------------------------------------->
            <div title="粘贴邮件列表" data-options="cache:false,
            iconCls:'fa fa-paste',
            href:'{$urlHrefs.importSimple}'">
            </div>
            <!----------------------------------------------------------------------------------------------------->
            <div title="上传邮件列表文本文件" data-options="cache:false,
            iconCls:'fa fa-file-text',
            href:'{$urlHrefs.importFile}'">
            </div>
            <!----------------------------------------------------------------------------------------------------->
            <div title="上传邮件列表CSV文件" data-options="cache:false,
            iconCls:'fa fa-file-excel-o',
            href:'{$urlHrefs.importCsvfile}'">
            </div>
            <!----------------------------------------------------------------------------------------------------->
        </div>
    </div>
</div>
<script type="text/javascript">
    var subscribersImportModule = {
        getImportLists:function(){
            var listIds = [];
            var eSubscriberImportListsDefs = <?=json_encode(IndexDefs::$eSubscriberImportListsDefs)?>;
            for(var key in eSubscriberImportListsDefs){
                if($("#allListCheckbox_" + key).prop('checked')){
                    listIds.push(key);
                }
            }
            /////////////////////////////////////////////////////////////////
            var tabPanels = $("#listsAllTabs").tabs('tabs');
            for(var i=0; i<tabPanels.length; i++){
                var tabId = tabPanels[i].panel('options').id;
                if(tabId == '0'){
                  continue;
                }
                var rows = $('#listsDatalist_' + tabId).datalist('getChecked');
                if(rows.length == 0){
                  continue;
                }
                for(var j=0; j<rows.length; j++){
                  listIds.push(rows[j].value);
                }
            }
            return listIds.join(',');
        }
    };
</script>