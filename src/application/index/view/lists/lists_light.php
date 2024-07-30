<table class="table-form">
    <?php foreach($lists as $list){ ?>
    <tr>
        <td class="field-label"><?=$list['name']?></td>
        <td class="field-input">
            <span class="badge badge-success" title="订阅者数量"><?=$list['subscriber_count']?></span>
            <span class="badge badge-default text-deleted" title="黑名单数量"><?=$list['subscriber_blacklisted_count']?></span>
        </td>
    </tr>
    <?php } ?>
</table>