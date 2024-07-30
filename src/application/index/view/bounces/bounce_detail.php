<?php
use app\index\Defs as IndexDefs;
?>
<table class="table-form">
    <tr>
        <td width="20%" class="field-label">退信日期</td>
        <td class="field-value">{$bounce.date}</td>
    </tr>
    <tr>
        <td class="field-label">消息头</td>
        <td class="field-value">{$bounce.header}</td>
    </tr>
    <tr>
        <td class="field-label">消息体</td>
        <td class="field-value">
            <iframe width="100%" height="300" src="{$urlHrefs.bouncePreviewBody}" frameborder="1"></iframe>
        </td>
    </tr>
    <tr>
        <td class="field-label">状态</td>
        <td class="field-value"><?=IndexDefs::$eBounceStatusDefs[$bounce['processed_status']]?></td>
    </tr>
    <tr>
        <td class="field-label">备注</td>
        <td class="field-value">{$bounce.comment}</td>
    </tr>
</table>