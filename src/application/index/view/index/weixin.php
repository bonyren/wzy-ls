<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=systemSetting('SYSTEM_TITLE')?></title>
    <link rel='shortcut icon' href='__STATIC__/favicon.ico' />
    <script type="text/javascript">
        var SITE_URL = '<?=SITE_URL?>';
        var STATIC_VER = '<?=STATIC_VER?>';
    </script>
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/bootstrap/v4/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/mobile.css?<?=STATIC_VER?>"/>
    <script type="text/javascript" src="__STATIC__/js/jquery.min.js"></script>
    <script type="text/javascript" src="__STATIC__/js/clipboard.min.js"></script>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="content-center">
			<div class="jumbotron">
			  <h1 class="display-4">欢迎访问<?=systemSetting('SYSTEM_TITLE')?></h1>
			  <p class="lead">请用电脑浏览器访问如下网址:</p>
			  <hr class="my-4">
			  <p><?=$siteUrl?></p>
                <button class="btn btn-primary btn-lg" data-clipboard-text="<?=$siteUrl?>">
                    拷贝网址
                </button>
			</div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var clipboard = new ClipboardJS('.btn');
    clipboard.on('success', function(e) {
        console.log(e);
        alert('拷贝成功');
    });
    clipboard.on('error', function(e) {
        console.log(e);
    });
</script>
</body>
</html>