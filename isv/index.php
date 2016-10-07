<!DOCTYPE html>
<?php
require_once(__DIR__ . "/jssdk.php");
$corpId =  $_GET['corpid'];
?>

<html>
    <head>
        <title>isv demo</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
        <link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="./public/css/style.css" type="text/css" />
    </head>

    <body>
        <div class = "well">
            <button class="btn btn-primary btn-block" onclick="location.reload(true)">刷新</button>
            <button class="btn btn-default btn-block showProfile">查看个人资料页</button>
            <button class="btn btn-default btn-block getLocation">获取当前地理位置</button>
            <button class="btn btn-default btn-block chooseEmpl">发送应用消息</button>
        </div>
    </body>

    <script> var _config = <?php echo JSSDK::getConfig($corpId);?> </script>
    <script src="http://fastcdn.imoffice.com/js/zepto/1.1.6/zepto.min.js"></script>
    <script src="http://fastcdn.imoffice.com/js/imo-jssdk/imoMobile/1.1.0/imoMobile.js"></script>

    <script type="text/javascript" src="./public/javascripts/logger.js"></script>
    <script type="text/javascript" src="./public/javascripts/demo.js"></script>
</html>
