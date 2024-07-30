<div class="easyui-layout" data-options="fit:true">
    <div id="hyperlinks" data-options="region:'west',title:''" style="width:60%;">
        <table id="campaignHyperlinksDatagrid" class="easyui-datagrid" data-options="striped:true,
            nowrap:false,
            autoRowHeight:true,
            singleSelect:true,
            url:'{$urlHrefs.campaignHyperlinks}',
            method:'post',
            pagination:false,
            border:false,
            fit:true,
            fitColumns:true,
            title:'',
            rowStyler:campaignHyperlinksModule.rowCampaignStyler,
            onSelect:function(index, row){
                campaignHyperlinksModule.loadSubscriberHyperlinks(row.url, row.link_track_id);
            },
            onLoadSuccess:function(row, data){
                $('#campaignHyperlinksDatagrid').datagrid('selectRow', 0);
            }
            ">
            <thead>
            <tr>
                <th data-options="field:'url_style',width:200,align:'left'">连接</th>
                <th data-options="field:'total_count_style',width:60,align:'center'">投递次数</th>
                <th data-options="field:'clicked_count_style',width:100,align:'center'">点击次数</th>
                <th data-options="field:'click_time_style',width:120,align:'center'">点击时间</th>
            </tr>
            </thead>
        </table>
    </div>
    <div id="subscriberHyperlinks" data-options="region:'center',title:''">
        <table id="subscriberHyperlinksDatagrid" class="easyui-datagrid" data-options="striped:true,
            nowrap:false,
            title:'',
            autoRowHeight:true,
            singleSelect:true,
            url:'',
            method:'post',
            pagination:true,
            pageSize:<?=DEFAULT_PAGE_ROWS?>,
            pageList:[10,20,30,50,80,100],
            border:false,
            fit:true,
            fitColumns:true,
            rowStyler:campaignHyperlinksModule.rowSubscriberStyler,
            onLoadSuccess:function(row, data){
            }
            ">
            <thead>
            <tr>
                <th data-options="field:'email',width:100,align:'left'">订阅者邮箱</th>
                <th data-options="field:'clicked_count_style',width:100,align:'center'">点击次数</th>
                <th data-options="field:'click_time_style',width:100,align:'center'">点击时间</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var campaignHyperlinksModule = {
        rowCampaignStyler:function(index, row){
            row.url_style = '<a href="'  + row.url + '" target="_blank">' + row.url + '</a>';

            row.total_count_style = '<span class="badge badge-success my-1" title="总投递次数">' + row.total + '</span>';

            row.clicked_count_style = '<span class="badge badge-primary my-1" title="总点击次数">' + row.clicked + '</span> | ' +
                '<span class="badge badge-secondary my-1" title="HTML邮件点击次数">' + row.html_clicked + '</span> | ' +
                '<span class="badge badge-info my-1" title="纯文本邮件点击次数">' + row.text_clicked + '</span>';

            row.click_time_style = '首次:' + '<span class="badge badge-light my-1" title="首次点击时间">' + row.first_click + '</span><br />' +
                '最后:' + '<span class="badge badge-light my-1" title="最后点击时间">' + row.latest_click + '</span>';
        },
        rowSubscriberStyler:function(index, row){
            row.clicked_count_style = '<span class="badge badge-primary my-1" title="总点击次数">' + row.clicked + '</span> | ' +
                '<span class="badge badge-secondary my-1" title="HTML邮件点击次数">' + row.html_clicked + '</span> | ' +
                '<span class="badge badge-info my-1" title="纯文本邮件点击次数">' + row.text_clicked + '</span>';

            row.click_time_style = '首次:' + '<span class="badge badge-light my-1" title="首次点击时间">' + row.first_click + '</span><br />' +
                '最后:' + '<span class="badge badge-light my-1" title="最后点击时间">' + row.latest_click + '</span>';
        },
        loadSubscriberHyperlinks:function(linkUrl, linkTrackId){
            var url = '{$urlHrefs.subscribersHyperlinks}';
            url = GLOBAL.func.addUrlParam(url, 'linkTrackId', linkTrackId);
            $("#subscriberHyperlinksDatagrid").datagrid({
                url:url,
                title:'点击详情 - ' + linkUrl
            });
        }
    };
</script>I