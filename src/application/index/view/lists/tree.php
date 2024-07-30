<?php
use app\index\Defs as IndexDefs;
?>
<div style="height:30px;" class="d-flex justify-content-around">
    <div class="ml-1">
        <a href="javascript:void(0)" class="easyui-menubutton" data-options="menu:'#listsMenu'">
            集合操作
        </a>
    </div>
    <div id="listsMenu" style="width:100px;">
        <div data-options="iconCls:'fa fa-plus fa-fw'" onclick="listsTreeModule.add()">新增</div>
        <div data-options="iconCls:'fa fa-pencil fa-fw'" onclick="listsTreeModule.edit()">编辑</div>
        <div data-options="iconCls:'fa fa-remove fa-fw'" onclick="listsTreeModule.delete()">删除</div>
    </div>
    <div class="mt-1 ml-1">
        <select id="listActiveCombobox" name="listActive" class="easyui-combobox"
                data-options="editable:false,panelHeight:'auto',onChange:function(newValue,oldValue){
                    listsTreeModule.onListActiveChange(newValue,oldValue);
                }" style="height:20px;width:65px;">
            <option value="0">不限</option>
            <?php foreach(IndexDefs::$eListActiveDefs as $key=>$value){?>
                <option value="<?=$key?>" <?php if($key == IndexDefs::eListActive) echo 'selected'; ?>><?=$value?></option>
            <?php }?>
        </select>
    </div>
    <div class="mt-1 mr-1">
        <span class="btn-group">
            <a href="javascript:;" class="btn btn-light size-MINI" onclick="listsTreeModule.upDown(1)"
               style="height: 16px;line-height: 16px" title="上移"><i class="fa fa-arrow-up"></i>
            </a>
            <a href="javascript:;" class="btn btn-light size-MINI" onclick="listsTreeModule.upDown(-1)"
               style="height: 16px;line-height: 16px" title="下移"><i class="fa fa-arrow-down"></i>
            </a>
        </span>
    </div>
</div>
<div class="divide-border"></div>
<div id="listsTree" class="easyui-tree"
     data-options="url:'{$urlHrefs.tree}',
        animate:true,
        lines:true,
        border:false,
        queryParams:{
            active:<?=IndexDefs::eListActive?>
        },
        onSelect:function(node){
            listsLayoutModule.openUrl(node.id, node.text);
            listsTreeModule.onSelected(node);
        },
        onLoadSuccess:function(node,data){
            listsTreeModule.restoreSelectedNode(node, data);
        },
        formatter:function(node){
            return listsTreeModule.formatNode(node);
        }">
</div>
<script type="text/javascript">
var listsTreeModule = {
	dialog:'#globel-dialog-div',
    tree:'#listsTree',
    add:function(){
    	var that = this;
        var href = '{$urlHrefs.add}';
        $(that.dialog).dialog({
            title: '新增集合',
            iconCls: 'fa fa-plus-circle',
            width: <?=$loginMobile?"'90%'":450?>,
            height: 300,
            cache: false,
            href: href,
            modal: true,
            collapsible: false,
            minimizable: false,
            resizable: false,
            maximizable: false,
            buttons:[{
                text:'确定',
                iconCls:iconClsDefs.ok,
                handler: function(){
                    $(that.dialog).find('form').eq(0).form('submit', {
                        onSubmit: function(){
                            var isValid = $(this).form('validate');
                            if (!isValid) return false;
                            $.messager.progress({text:'处理中，请稍候...'});
                            $.post(href, $(this).serialize(), function(res){
                                $.messager.progress('close');
                                if(!res.code){
                                    $.app.method.alertError(null, res.msg);
                                }else{
                                    $.app.method.tip('提示', res.msg, 'info');
                                    $(that.dialog).dialog('close');
                                    that.reload();
                                }
                            }, 'json');
                            return false;
                        }
                    });
                }
            },{
                text:'取消',
                iconCls:iconClsDefs.cancel,
                handler: function(){
                    $(that.dialog).dialog('close');
                }
            }]
        });
        $(that.dialog).dialog('center');
    },
    edit:function(){
		var that = this;
        var selectedNode = $(that.tree).tree('getSelected');
        if(!selectedNode || selectedNode.id == 0){
            $.app.method.tip('提示', "请选择要修改的集合", 'error');
            return;
        }
        var listId = selectedNode.id;
        var Name = selectedNode.text;
        if(listId == 0){
            return;
        }
        var href = '{$urlHrefs.edit}';
        href = GLOBAL.func.addUrlParam(href, 'listId', listId);
        $(that.dialog).dialog({
            title: '修改集合 - ' + Name,
            iconCls: 'fa fa-pencil-square',
            width: <?=$loginMobile?"'90%'":450?>,
            height: 300,
            cache: false,
            href: href,
            modal: true,
            collapsible: false,
            minimizable: false,
            resizable: false,
            maximizable: false,
            buttons:[{
                text:'确定',
                iconCls:iconClsDefs.ok,
                handler: function(){
                    $(that.dialog).find('form').eq(0).form('submit', {
                        onSubmit: function(){
                            var isValid = $(this).form('validate');
                            if (!isValid) return false;
                            $.messager.progress({text:'处理中，请稍候...'});
                            $.post(href, $(this).serialize(), function(res){
                                $.messager.progress('close');
                                if(!res.code){
                                    $.app.method.alertError(null, res.msg);
                                }else{
                                    $.app.method.tip('提示', res.msg, 'info');
                                    $(that.dialog).dialog('close');
                                    that.reload();
                                }
                            }, 'json');
                            return false;
                        }
                    });
                }
            },{
                text:'取消',
                iconCls:iconClsDefs.cancel,
                handler: function(){
                    $(that.dialog).dialog('close');
                }
            }]
        });
        $(that.dialog).dialog('center');
	},
    delete:function(){
    	var that = this;
        var selectedNode = $(that.tree).tree('getSelected');
        if(!selectedNode || selectedNode.id == 0){
            $.app.method.tip('提示', "请选择要删除的集合", 'error');
            return;
        }
        var listId = selectedNode.id;
        var Name = selectedNode.text;
        if(listId == 0){
            return;
        }
        var href = '{$urlHrefs.delete}';
        href = GLOBAL.func.addUrlParam(href, 'listId', listId);
        $.messager.confirm('提示', `确认删除集合"${Name}"吗?`, function(result){
            if(!result) return false;
            $.messager.progress({text:'处理中，请稍候...'});
            $.post(href, {}, function(res){
                $.messager.progress('close');
                if(!res.code){
                    $.app.method.alertError(null, res.msg);
                }else{
                    $.app.method.tip('提示', res.msg, 'info');
                    that.reload();
                }
            }, 'json');
        });  
    },
    reload:function(){
        $(this.tree).tree('reload');
    },
    formatNode:function(node){
        if(node.id == 0){
            return node.text;
        }
        var text = '';
        if(node.active == <?=IndexDefs::eListInactive?>){
            text += ' <span class="text-deleted bg-light">';
        }else{
            text += ' <span>';
        }
        text += node.text;
        text += '</span>';
        text += `|<span class="text-success font-weight-bold">${node.subscriber_count}</span> <span class="text-muted font-weight-bold text-deleted">${node.subscriber_blacklisted_count}</span>|`;
        return text;
    },
    onSelected:function(node){
        if(node.id == 0 || node.id == -1){
            GLOBAL.listTree.lastSelectedNodeId = String(node.id);
        }else{
            GLOBAL.listTree.lastSelectedNodeId = '0_' + node.id;
        }
    },
    onListActiveChange:function(newValue,oldValue){
        var that = this;
        var queryParams = $(that.tree).tree('options').queryParams;
        queryParams['active'] = newValue;
        that.reload();
    },
    upDown:function(direction){
        var that = this;
        var selectedNode = $(that.tree).tree('getSelected');
        if(!selectedNode){
            return;
        }
        var id = selectedNode.id;
        if(id == 0){
            return;
        }
        var href = '{$urlHrefs.updateOrder}';
        href = GLOBAL.func.addUrlParam(href, 'id', id);

        $.messager.progress({text:'处理中，请稍候...'});
        $.post(href, {upDown:direction}, function(res){
            $.messager.progress('close');
            if(!res.code){
                $.app.method.alertError(null, res.msg);
            }else{
                $.app.method.tip('提示', res.msg, 'info');
                that.reload();
            }
        }, 'json');
    },
    selectList:function(listId){
        var that = this;
        var target = this.getNodeByIdAdnTypeRecursively(listId,null);
        if(target){
            $(that.tree).tree('select', target);
        }
    },
    restoreSelectedNode: function(node, data){
        var that = this;
        var selectedNodeTarget = null;
        var nodeIds = GLOBAL.listTree.lastSelectedNodeId.split('_');
        var nodes = $(that.tree).tree('getRoots');
        for(var i=0; i<nodeIds.length; i++){
            var nodeId = nodeIds[i];
            var bFind = false;
            for(var j=0; j<nodes.length; j++){
                if(nodeId == nodes[j].id){
                    selectedNodeTarget = nodes[j].target;
                    nodes = $(that.tree).tree('getChildren', nodes[j].target);
                    bFind = true;
                    break;
                }
            }
            if(!bFind){
                break;
            }
        }
        if(selectedNodeTarget){
            $(that.tree).tree('select', selectedNodeTarget);
        }else{
            var roots = $(that.tree).tree('getRoots');
            if(roots){
                $(that.tree).tree('select', roots[0].target);
            }
        }
    },
    getNodeByIdAdnTypeRecursively: function(targetIdSelf, target){
        if(target == null){
            var nodes = $(this.tree).tree('getRoots');
        }else{
            var nodes = $(this.tree).tree('getChildren', target);
        }
        if(!nodes){
            return null;
        }
        var findTarget = null;
        for(var i=0; i<nodes.length; i++){
            if(targetIdSelf == nodes[i].id){
                findTarget = nodes[i].target;
                break;
            }else{
                findTarget = this.getNodeByIdAdnTypeRecursively(targetIdSelf, nodes[i].target);
                if(findTarget){
                    break;
                }
            }
        }
        return findTarget;
    }
};
</script>