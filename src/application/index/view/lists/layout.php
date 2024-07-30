<div class="easyui-layout" data-options="fit:true">
    <div data-options="region:'west',href:'{$urlHrefs.tree}',minWidth:180"
		 style="width:20%;">
	</div>
    <div id="layoutCenter" data-options="region:'center',href:'',title:' '">
	</div>
</div>

<script type="text/javascript">
var listsUrl = '{$urlHrefs.lists}';
var subscribersUrl = '{$urlHrefs.subscribers}';
var listsLayoutModule = {
	openUrl: function(id, title){
		var url = '';
		var icon = ''
		if(0 == id){
			url = listsUrl;
			title = title + "下的集合";
			icon = "fa fa-list";
		}else{
			url = subscribersUrl;
			url = GLOBAL.func.addUrlParam(url, 'listId', id);
			title = title + "下的订阅者";
			icon = "fa fa-users";
		}
		/*the following can work well, but can't change the panel icon dynamically*/
		//$('#layoutCenter').panel('setTitle', title);
		//$('#layoutCenter').panel('refresh', url);
		$('#layoutCenter').panel({
			title:title,
			href:url,
			iconCls:icon
		});
		return true;
	}
}

</script>