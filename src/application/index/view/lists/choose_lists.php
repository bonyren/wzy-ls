<?php
use app\index\Defs as IndexDefs;
?>
<table id="listsTreegrid" class="easyui-treegrid"
       data-options="url:'{$urlHrefs.chooseLists}',
                    title:'',
                    animate:true,
                    lines:false,
                    border:true,
                    idField:'id',
                    treeField:'text',
                    fit:true,
                    fitColumns:true,
                    checkbox:true,
                    singleSelect:true,
                    checkOnSelect:true,
                    selectOnCheck:true,
                    onlyLeafCheck:true,
                    onLoadSuccess:function(node,data){
                    },
                    onCheckNode:function(row,checked){
                        if(checked){
                            console.log('onCheckNode - checked');
                        }else{
                            console.log('onCheckNode - unchecked');
                        }
                        chooseListsModule.onListsCheckNode(row, checked);
                    },
                    onLoadSuccess:function(row, data){

                    }
                    ">
    <thead>
    <tr>
        <th data-options="field:'text',width:180">集合名称</th>
        <th data-options="field:'formatMembers',width:200,align:'center',formatter:chooseListsModule.formatMembers">订阅者数量</th>
    </tr>
    </thead>
</table>
<script type="text/javascript">
    var chooseListsModule = {
        treegrid:'#listsTreegrid',
        choosedListIds:[],
        formatMembers:function(val, row){
            return  '<span class="badge badge-success" title="订阅者数量">' +  row.subscriber_count + '</span>';
        },
        onListsCheckNode:function(row, checked){
            var that = this
            if(checked) {
                that.choosedListIds.push(row.list_id);
            }else{
                that.choosedListIds = $.grep(that.choosedListIds, function(n, i){
                    return n != row.listId;
                });
            }
        },
        getChoosedLists:function(){
            var that = this;
            var lists = [];
            var nodes = $(that.treegrid).treegrid('getCheckedNodes');
            for(var i= 0, len = nodes.length; i<len; i++){
                lists.push({
                    list_id:nodes[i].list_id,
                    name:nodes[i].name,
                    subscriber_count:nodes[i].subscriber_count,
                    subscriber_blacklisted_count:nodes[i].subscriber_blacklisted_count
                });
            }
            return lists;
        }
    }
</script>