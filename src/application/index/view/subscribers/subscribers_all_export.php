<?php
use app\index\Defs as IndexDefs;
?>
<form id="subscribersExportForm">
	<div class="easyui-panel" data-options="border:false">
		<header>
			第1步: 根据日期筛选
		</header>
		<div class="p-2">
			<div class="p-1">
				<input type="radio" name="dateType" id="anyDateRadio" value="<?=IndexDefs::eSubscriberExportDateAny?>" />
				所有日期 (导出所有订阅者)
			</div>
			<div class="p-1">
				<input type="radio" name="dateType" value="<?=IndexDefs::eSubscriberExportDateChanged?>" />记录最后更新时间
			</div>
			<div id="dateFromToDiv" class="pl-5">
				开始:<input id="dateFrom" name="dateFrom" class="easyui-datebox" data-options="required:true,editable:false" value="{$bindValues.defaultDate}" />
				结束:<input id="dateTo" name="dateTo" class="easyui-datebox" data-options="required:true,editable:false" value="{$bindValues.defaultDate}" />
			</div>
		</div>
	</div>
	<!----------------------------------------------------------------------------------------------------------------->
	<div class="easyui-panel" data-options="border:false">
		<header>
			第2步: 选择导出的列
		</header>
		<div>
			<div id="subscriberFieldsDatalistToolbar">
				<input type="checkbox" id="checkAllCheckbox" value="1"/>全选
			</div>
			<ul id="subscriberFieldsDatalist" class="easyui-datalist" data-options="checkbox:true,
					height:220,
	                fitColumns:true,
	                lines:true,
	                striped:true,
	                singleSelect:false,
	                border:false,
	                toolbar:'#subscriberFieldsDatalistToolbar',
	                onLoadSuccess:function(node,data){
                    	subscribersExportModule.init();
                	}">
				{volist name="$bindValues.fields" id="field"}
				<li value="{$key}">{$field}</li>
				{/volist}
			</ul>
		</div>
	</div>
	<p class="text-center p-1">
		<a id="subscribersExportBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'fa fa-share-square-o',size:'large',
        		onClick:function(){
        			subscribersExportModule.downloadCSV();
        		}">导出
		</a>
	</p>
</form>
<script type="text/javascript">
	$('input[name="dateType"]').on('change', function(){
		var val = parseInt($(this).val());
		if(val == <?=IndexDefs::eSubscriberExportDateAny?>) {
			$("#dateFromToDiv").hide();
		}else{
			$("#dateFromToDiv").show();
		}
	});
	$('#checkAllCheckbox').change(function(){
		if(this.checked){
			$("#subscriberFieldsDatalist").datalist('checkAll');
		}else{
			$("#subscriberFieldsDatalist").datalist('uncheckAll');
		}
	});
	var subscribersExportModule = {
		init:function(){
			$('#anyDateRadio').prop('checked', true).trigger('change');
			$("#subscriberFieldsDatalist").datalist('checkAll');
		},
		downloadCSV:function(){
			var that = this;
			var queryString = '';
			var dateType = $('input[name="dateType"]:checked').val();
			queryString += 'dateType=';
			queryString += encodeURIComponent(dateType);

			if(dateType == <?=IndexDefs::eSubscriberExportDateChanged?>){
				var dateFrom = $("#dateFrom").datebox('getValue');
				var dateTo = $("#dateTo").datebox('getValue');
				if(!dateFrom || !dateTo){
					$.messager.alert('提示', '起止日期不能为空', 'info');
					return;
				}
				queryString += '&dateFrom=';
				queryString += encodeURIComponent(dateFrom);
				queryString += "&dateTo=";
				queryString += encodeURIComponent(dateTo);
			}
			var fieldIds = [];
			var rows = $("#subscriberFieldsDatalist").datalist("getChecked");
			if(rows.length == 0){
				$.messager.alert('提示', '请选择导出列', 'info');
				return;
			}
			for(var i=0; i<rows.length; i++){
				fieldIds.push(rows[i].value);
			}
			queryString += "&fields=";
			queryString += encodeURIComponent(fieldIds.join(','));
			var href = '{$urlHrefs.subscribersDownloadCSV}';
			href += href.indexOf('?') != -1 ? '&' + queryString : '?' + queryString;
			window.open(href);
		}
	};
</script>