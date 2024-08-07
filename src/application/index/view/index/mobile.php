<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=systemSetting('SYSTEM_TITLE')?></title>
    <link rel='shortcut icon' href='__STATIC__/favicon.ico' />
    <script type="text/javascript">
        var SITE_URL = '<?=SITE_URL?>';
        var STATIC_VER = '<?=STATIC_VER?>';
    </script>
    <link rel="stylesheet" type="text/css" href="__STATIC__/js/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="__STATIC__/js/easyui/themes/mobile.css">
    <link rel="stylesheet" type="text/css" href="__STATIC__/js/easyui/themes/color.css"/>
    <link rel="stylesheet" type="text/css" href="__STATIC__/js/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/index/default.css?<?=STATIC_VER?>"/>
    <link rel="stylesheet" type="text/css" href="__STATIC__/bootstrap/v4/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/components.css?<?=STATIC_VER?>"/>
    <script type="text/javascript" src="__STATIC__/js/jquery.min.js"></script>
    <script type="text/javascript" src="__STATIC__/js/easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="__STATIC__/js/easyui/jquery.easyui.mobile.js"></script>
    <script type="text/javascript" src="__STATIC__/js/easyui/locale/easyui-lang-zh_CN.js"></script>
    <script type="text/javascript" src="__STATIC__/highcharts/code/highcharts.js"></script>
    <script>
    var iconClsDefs = <?=json_encode(\app\index\Defs::$iconClsDefs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
    </script>
    <style>
        *, ::after, ::before {box-sizing: border-box;}
        #mm{width:100%;height:100%;position:fixed;top:0;left:0;z-index:999;opacity:0;}
        #m-left-menu{width:180px;height:100%;border-right:1px solid #95B8E7;}
        #m-menu-mask{z-index:998;position:fixed;display:none;}
        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            border-radius: .25rem;
            line-height: 1.8rem;
            margin: 5px;
            padding: 0;
            background: #ecf0f5;
        }
        .breadcrumb-item+.breadcrumb-item::before {
            display: inline-block;
            padding: 0 0.5rem;
            color: #6c757d;
            content: ">";
        }
    </style>
</head>

<body>
<div class="easyui-navpanel">
    <header>
        <div class="m-toolbar">
            <div class="m-left">
                <?php if($loginUserType == \app\index\Defs::LOGIN_USER_ADMIN_TYPE){ ?>
                    <a href="javascript:void(0)" class="easyui-linkbutton"
                       onclick="mApp.toggleMenu()" data-options="plain:true,iconCls:'fa fa-bars'"></a>
                <?php } ?>
            </div>
            <div class="m-title"><?=systemSetting('SYSTEM_TITLE')?></div>
            <div class="m-right">
                <a href="javascript:;" class="easyui-splitbutton" data-options="menu:'#toparea-user-info-box',
                	iconCls:'fa fa-user-circle'">{$loginUserInfos['realname']}
                </a>
                <div id="toparea-user-info-box">
                    <div data-options="iconCls:'fa fa-key'" onclick="mApp.password()">修改密码</div>
                    <div class="menu-sep"></div>
                    <div data-options="iconCls:'fa fa-sign-out'" onclick="mApp.logout()">登出</div>
                </div>
            </div>
        </div>
    </header>
    <div id="m-content" class="easyui-navpanel m-content-box" data-options="href:'{$urlHrefs.main}',title:''"></div>
</div>


<div id="mm">
    <div id="m-left-menu" class="easyui-accordion" data-options="border:false,selected:0">
        <?php foreach ($menus as $v): if(empty($v['children'])){ continue; } ?>
        <div title="<?=$v['name']?>">
            <ul class="easyui-tree" data-options='
                data:<?=json_encode($v['children'],JSON_UNESCAPED_UNICODE)?>,
                animate:true,
                lines:true,
                onClick:mApp.openUrl'></ul>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 公共部分 -->
<div id="m-menu-mask" class="window-mask"></div>

<div id="globel-dialog-div" class="word-wrap" style="line-height:1.5"></div>
<div id="globel-dialog2-div" class="word-wrap" style="line-height:1.5"></div> <!-- 特殊情况可能需要弹出第2个弹出层 -->
<div id="globel-dialog3-div" class="word-wrap" style="line-height:1.5"></div> <!-- 特殊情况可能需要弹出第3个弹出层 -->

<script type="text/javascript" src="__STATIC__/js/easyui/extension/jquery-easyui-datagridview/datagrid-detailview.js"></script>
<script type="text/javascript" src="__STATIC__/js/easyui/extension/jquery-easyui-datagridview/datagrid-groupview.js"></script>
<script type="text/javascript" src="__STATIC__/js/jquery.json.min.js"></script>
<script type="text/javascript" src="__STATIC__/js/jquery.app.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/common.js?<?=STATIC_VER?>"></script>
<script type="text/javascript" src="__STATIC__/js/components.js?<?=STATIC_VER?>"></script>
<script type="text/javascript">
var mApp = {
	dialog: '#globel-dialog-div',
	dialog2: '#globel-dialog2-div',
	//初始化
	init:function(){
	    $('#mm').hide().css('opacity',1);
	    $('#mm').click(function(){
            mApp.toggleMenu();
            return false;
        });
        mApp.updateCss();
        this.sessionLife();
	},
    toggleMenu:function(){
        if($('#mm').is(':visible')){
            $('#m-menu-mask').hide();
            $('#mm').hide();
        } else {
            $('#m-menu-mask').show();
            $('#mm').slideDown();
        }
    },
	openUrl:function(node){
		if (undefined === node.url) {
			return false;
		}
		$('#m-content').panel({
			title: node.attributes.breadcrumb,
			href: node.url,
			// iconCls: node.iconCls
		});
		mApp.toggleMenu();
		mApp.updateCss();
	},
    updateCss:function(){
        $('#m-content').prev().css({background:'#EFF5FF ',paddingLeft:'10px'}).children('.panel-title').css({fontWeight:'normal',color:'#666'});
    },
    //退出登录
    logout: function(){
        $.messager.confirm('提示信息', '确定要退出登录吗？', function(y){
            if (!y) {
                return;
            }
            $.messager.progress({text:'处理中，请稍候...'});
            $.post("{$urlHrefs.logout}", function(data){
                $.messager.progress('close');
                if(data.code){
                    window.location.href = data.data;
                }
            });
        });
    },
    //防止登录超时
    sessionLife: function(){
        setInterval(function(){
            $.post("{$urlHrefs.publicSessionLife}", function(data){
                if(data.code == 0){
                    $.messager.show({
                        title: '系统提示',
                        msg: data.msg,
                        timeout:3000,
                        showType:'slide'
                    });
                    setTimeout(function(){
                        window.location.href = data.data;
                    }, 3000);
                }
            }, 'json');
        }, 15000);
    },
    //修改密码
    password: function(){
        var that = this;
        $(that.dialog).dialog({
            title: '修改登录密码',
            iconCls: iconClsDefs.edit,
            width: '90%',
            height: '50%',
            cache: false,
            href: "{$urlHrefs.modifyPwd}",
            modal: true,
            collapsible: false,
            minimizable: false,
            resizable: false,
            maximizable: false,
            buttons:[{
                text: '确定',
                iconCls: iconClsDefs.ok,
                handler: function(){
                    $(that.dialog).find('form').eq(0).form('submit', {
                        onSubmit: function(){
                            var isValid = $(this).form('validate');
                            if (!isValid) return false;

                            $.messager.progress({text:'处理中，请稍候...'});
                            $.post('{$urlHrefs.modifyPwd}', $(this).serialize(), function(res){
                                $.messager.progress('close');
                                if(!res.code){
                                    $.app.method.alertError(null, res.msg);
                                }else{
                                    $.messager.confirm('提示', res.msg, function(result){
                                        if(result) window.location.href = res.data;
                                    });
                                }
                            }, 'json');

                            return false;
                        }
                    });
                }
            },{
                text: '取消',
                iconCls: iconClsDefs.cancel,
                handler: function(){
                    $(that.dialog).dialog('close');
                }
            }]
        });
        $(that.dialog).dialog('center');
    }
};
$(function(){
	mApp.init();
	$.extend($.fn.validatebox.defaults.rules, {
		mobile: {
			validator:function(value,param){
				var re = /^1[23456789]\d{9}$/;
				if(re.test(value)){
					return true;
				}else{
					return false;
				}
			},
			message:'请填写正确格式的手机号码'
		},
		domain:{
			validator:function(value,param){
				var re = /^[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/;
				if(re.test(value)){
					return true;
				}else{
					return false;
				}
			},
			message:'请填写正确的域名格式'
		},
        date: {
            validator: function(value){
                var regex = /^\d{4}-\d{1,2}-\d{1,2}$/;
                return regex.test(value);
            },
            message: '请按正确的格式填写日期如：2017-01-01'
        },
        greater: {
            validator:function(value,param){
                if(value-$(param[0]).val() >= 0){
                    return true;
                }else{
                    return false;
                }
            },
            message:'数值过小'
        },
        lesser: {
            validator:function(value,param){
                if(value-$(param[0]).val() <= 0){
                    return true;
                }else{
                    return false;
                }
            },
            message:'数值过大'
        }
	});
});
var DG_ROW_CSS = {
    rowGray: 'color:#999;background-color:#F3F3F3;',
    rowWarn: 'color:#FF0000;background:#FFB90F;',
    rowError: 'color:#FF0000;background:#FFF8DC;',
    rowDel: 'text-decoration:line-through;background:#A1A1A1;'
};
GLOBAL.namespace('config');
GLOBAL.config.upload = <?=json_encode(config('upload'))?>;

function getEasyuiComponentType(target){
	var plugins = $.parser.plugins;
	for(var i=plugins.length-1; i>=0; i--){
		if ($(target).data(plugins[i])){
			return plugins[i];
		}
	}
	return null;
}
$(document).ajaxError(function (event, XMLHttpRequest, ajaxOptions){
    switch (XMLHttpRequest.status) {
        case 401: // Unauthorized
            // Take action, referencing XMLHttpRequest.responseText as needed.
            window.location.assign('<?=url('index/Index/login')?>');
            return false;
            break;
    }
});
</script>
</body>
</html>