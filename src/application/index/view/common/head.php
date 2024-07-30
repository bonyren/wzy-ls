<link rel='shortcut icon' href='/favicon.ico' />
<script type="text/javascript">
var SITE_URL = '<?=SITE_URL?>';
var STATIC_VER = '<?=STATIC_VER?>';
</script>
{include file="common/theme"/}
<script type="text/javascript" src="__STATIC__/js/jquery.min.js"></script>
<script type="text/javascript" src="__STATIC__/js/jquery.cookie.js"></script>
<script type="text/javascript" src="__STATIC__/js/jquery.json.min.js"></script>

<script type="text/javascript" src="__STATIC__/js/easyui/jquery.easyui.min.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/easyui/locale/easyui-lang-zh_CN.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/easyui/extension/jquery-easyui-datagridview/datagrid-detailview.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/easyui/extension/jquery-easyui-datagridview/datagrid-groupview.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/easyui/extension/jquery-easyui-texteditor/jquery.texteditor.js?<?=STATIC_VER?>"></script>

<script type="text/javascript" src="__STATIC__/js/jquery.app.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/common.js?<?=STATIC_VER?>"></script>

<script type="text/javascript" src="__STATIC__/highcharts/code/highcharts.js"></script>
<script type="text/javascript" src="__STATIC__/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="__STATIC__/js/leftTime.min.js"></script>
<script type="text/javascript" src="__STATIC__/js/sprintf.js"></script>
<script type="text/javascript" src="__STATIC__/js/pdfobject.js"></script>
<script type="text/javascript" src="__STATIC__/js/components.js?<?=STATIC_VER?>"></script>
<script>
    var iconClsDefs = <?=json_encode(\app\index\Defs::$iconClsDefs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
    GLOBAL.namespace('config');
    GLOBAL.config.upload = <?=json_encode(config('upload'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
</script>