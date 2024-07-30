<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="renderer" content="Blink|webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">
    <meta name="author" content="wzycoding">
    <title><?=systemSetting('SYSTEM_TITLE')?></title>
    {include file="common/head" /}
    <style>
        html {
            height: 100%;
        }
        body {
            background: url(__STATIC__/img/loginbg.png) 0% 0% / cover no-repeat;
            position: static;
        }
    </style>
</head>
<body>
    <div class="loader"></div>
    <div class="easyui-dialog" data-options="title:'请登录您的账号',
        width:<?=$loginMobile?"'95%'":'450'?>,
        closable:false,
        border:true,
        iconCls:'fa fa-user',
        buttons:[
            {text:'登录',iconCls:'fa fa-sign-in',handler:submit}
        ],
        onOpen:function(){
            init();
        }
        ">
        <form id='login-form' action="<?=$urls['login']?>" method="post">
            <div class="bg-light text-center p-2">
                <h3><img src="<?=systemSetting('ORGANIZATION_LOGO')?>" style="height: 32px;">&nbsp;<?=systemSetting('SYSTEM_TITLE')?></h3>
            </div>
            <div class="p-2">
                <input class="easyui-textbox" id="login_account" name="username" data-options="required:true,
                    label:'用户名',
                    validType:{length:[2,20]},
                    tipPosition:'bottom',
                    width:'100%',
                    labelWidth:60
                    ">
            </div>
            <div class="p-2">
                <input class="easyui-passwordbox" id="login_password"  name="password" data-options="required:true,
                    label:'密码',
                    validType:{length:[6,20]},
                    tipPosition:'bottom',
                    width:'100%',
                    labelWidth:60
                    ">
            </div>
            <?php if($login_captcha_enable){ ?>
            <div class="p-2">
                <input class="easyui-textbox" id="login_captcha" name="captcha" data-options="required:true,
                    label:'验证码',
                    validType:{length:[4,4]},
                    tipPosition:'bottom',
                    width:200,
                    labelWidth:60" />
                <img id="login-captcha-img" align="top" onclick="changeCode();return false;"
                           src="{$urls.captcha}" title="刷新验证码" style="cursor:pointer;border:1px solid #eeeeee;">
            </div>
            <?php } ?>
            <div class="p-2">
                <input class="easyui-checkbox" name="auto_login" value="1" data-options="label:'自动登录',labelPosition:'after'">
            </div>
        </form>
        <footer class="p-1"><?=systemSetting('POWER_BY_TEXT')?></footer>
    </div>
    {include file="common/foot" /}
    <script type="text/javascript">
        function changeCode(){
            var that = document.getElementById('login-captcha-img');
            if (that) {
                var src = that.src;
                src = src.replace(/&salt=[0-9.]+/,'');
                that.src = src + '&salt=' + Math.random();
            }
        }
        function init(){
            $('.loader').fadeOut();
        }
        function submit(){
            var isValid = $('#login-form').form('validate');
            if(!isValid){
                return false;
            }
            //为了兼容微信浏览器，所以采用网页提交
            $('#login-form').submit();
        }
        setTimeout(function() {
            $('#login_account').textbox('textbox').focus();
        }, 500);
        $(document).keyup(function(event){
            if(event.keyCode == 13){
                submit();
            }
        });
    </script>
</body>
</html>