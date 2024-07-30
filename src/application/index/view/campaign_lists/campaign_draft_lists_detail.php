
<table class="campaignListsDetailTable mt-3" style="height: 60px;">
    <tbody>
        <tr>
            <td colspan="3" class="bg-light">订阅者</td>
        </tr>
        <tr>
            <td>
                总共<div class="text-primary font-strong">{$subscribers.total_count}</div>
            </td>
            <td>Html<div class="text-primary font-strong">{$subscribers.html_count}</div></td>
            <td>Text<div class="text-primary font-strong">{$subscribers.text_count}</div></td>
        </tr>
    </tbody>
</table>
<style>
    .campaignListsDetailTable{
        font-size: 11px;
        color: #333;
        border-collapse: collapse;
    }
    .campaignListsDetailTable td{
        padding-left: 5px;
        width: 90px;
        text-align: center;
        border:1px solid #0094ff;
    }
</style>