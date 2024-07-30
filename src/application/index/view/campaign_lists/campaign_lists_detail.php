<div class="d-flex justify-content-around">
<table class="campaignListsDetailTable mt-2" style="height: 100px;">
    <tbody>
        <tr>
            <td colspan="3" class="bg-light">订阅者</td>
        </tr>
        <tr>
            <td>
                总共<div class="text-primary font-strong">{$subscribers.total_count}</div>
            </td>
            <td>Html<div class="text-primary font-strong">{$subscribers.html_count}</div></td>
            <td>文本<div class="text-primary font-strong">{$subscribers.text_count}</div></td>
        </tr>
    </tbody>
</table>
<table class="campaignListsDetailTable mt-2" style="height: 100px;">
    <tbody>
        <tr>
            <td colspan="6" class="bg-light">投递结果</td>
        </tr>
        <tr>
            <td rowspan="2">
                耗时<div class="text-primary font-strong">{$sent.sent_time}</div>
            </td>
            <td rowspan="2">
                成功投递<div class="text-success font-strong">{$sent.success_count}</div>
            </td>
            <td>Html<div class="text-primary font-strong">{$sent.success_html_count}</div></td>
            <td rowspan="2">
                等待投递<div class="text-primary font-strong">{$sent.waiting_count}</div>
            </td>
            <td rowspan="2">
                未投递<div class="text-warning font-strong">{$sent.not_sent_count}</div>
            </td>
            <td rowspan="2">
                投递失败<div class="text-danger font-strong">{$sent.fail_sent_count}</div>
            </td>
        </tr>
        <tr>
            <td>文本<div class="text-primary font-strong">{$sent.success_text_count}</div></td>
        </tr>
    </tbody>
</table>
<table class="campaignListsDetailTable mt-2" style="height: 100px;">
    <tbody>
        <tr>
            <td colspan="6" class="bg-light">反馈</td>
        </tr>
        <tr>
            <td>
                查看数量<div class="text-primary font-strong">{$feedback.viewed_count}</div>
            </td>
            <td>
                唯一查看数量<div class="text-primary font-strong">{$feedback.unique_viewed_count}</div>
            </td>
            <td>
                退回数量<div class="text-danger font-strong">{$feedback.bounce_count}</div>
            </td>
            <td>
                <a href="javascript:;" onclick="viewHyperlinkReport_{$campaignId}();">超链接报告</a>
            </td>
        </tr>
    </tbody>
</table>
</div>
<script>
    function viewHyperlinkReport_{$campaignId}(){
        campaignListsModule.viewHyperlinkReport({$campaignId}, '<?=htmlspecialchars($subject, ENT_QUOTES)?>');
    }
</script>
<style>
    .campaignListsDetailTable{
        font-size: 11px;
        color: #333;
        border-collapse: collapse;
    }
    .campaignListsDetailTable td{
        padding-left: 5px;
        width: 80px;
        text-align: center;
        border:1px solid #0094ff;
    }
</style>