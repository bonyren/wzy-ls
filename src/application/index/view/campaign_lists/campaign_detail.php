<?php
use app\index\Defs as IndexDefs;
?>
<table class="table-form">
    <tr class="form-caption">
        <td colspan="2">投递活动信息</td>
    </tr>
    <tr>
        <td width="20%" class="field-label">主题</td>
        <td class="field-value">{$campaignInfos.subject}</td>
    </tr>
    <tr>
        <td class="field-label">创建时间</td>
        <td class="field-value">{$campaignInfos.entered}</td>
    </tr>
    <tr>
        <td class="field-label">投递邮箱</td>
        <td class="field-value">{$campaignInfos.mailbox}</td>
    </tr>
    <tr>
        <td class="field-label">内容</td>
        <td class="field-value">{$campaignInfos.message_content}</td>
    </tr>
    <tr>
        <td class="field-label">内容页脚</td>
        <td class="field-value">{$campaignInfos.message_footer}</td>
    </tr>
    <tr>
        <td class="field-label">再次投递</td>
        <td class="field-value"><?=IndexDefs::$eRequeueIntervalDefs[$campaignInfos['requeue_interval']]?></td>
    </tr>
</table>
<table class="table-form">
    <tr class="form-caption">
        <td colspan="4">投递活动附件</td>
    </tr>
    <tr>
        <td class="field-label">名称</td>
        <td class="field-label">大小</td>
        <td class="field-label">类型</td>
        <td class="field-label">描述</td>
    </tr>
    <?php foreach($campaignAttaches as $campaignAttach){ ?>
    <tr>
        <td class="field-value"><?=$campaignAttach['name']?></td>
        <td class="field-value"><?=$campaignAttach['size']?> bytes</td>
        <td class="field-value"><?=$campaignAttach['mime_type']?></td>
        <td class="field-value"><?=$campaignAttach['description']?></td>
    </tr>
    <?php } ?>
</table>
<table class="table-form">
    <tr class="form-caption">
        <td colspan="4">投递活动目标订阅者集合</td>
    </tr>
    <tr>
        <td class="field-label">集合名称</td>
        <td class="field-label">订阅者数量</td>
    </tr>
    <?php foreach($campaignLists as $campaignList){ ?>
        <tr>
            <td class="field-value"><?=$campaignList['name']?></td>
            <td class="field-value"><span class="badge badge-primary"><?=$campaignList['subscriber_count']?></span></td>
        </tr>
    <?php } ?>
</table>