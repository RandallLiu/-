
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APPNAME?></title>
    <?=link_tag('resource/assets/css/icons/icomoon/styles.css?_=2.0')?>

    <?=link_tag('resource/assets/ncss/login2.css?_=2.0')?>
    <?=script_tag('resource/app/comm.js')?>
    <?=script_tag('resource/assets/js/core/libraries/jquery.min.js')?>
    <?=script_tag('resource/assets/js/plugins/loaders/pace.min.js')?>
    <?=script_tag('resource/assets/js/core/libraries/bootstrap.min.js')?>
    <?=script_tag('resource/assets/js/plugins/loaders/blockui.min.js')?>
    <?=script_tag('resource/assets/js/plugins/forms/validation/validate.min.js')?>
    <?=script_tag('resource/assets/js/plugins/forms/validation/localization/messages_zh.js')?>
    <?=script_tag('resource/assets/js/plugins/notifications/pnotify.min.js')?>
    <?=script_tag('resource/assets/js/core/app.js')?>
    <?=script_tag('resource/app/_login.js?_=1.0')?>

</head>
<body>
<!--登录区主体开始-->
<div id="main">
    <!--头像区开始-->
    <div id="header-border">
        <!--头像区内部结束-->
    </div>
    <form class="form-login">
        <!--头像区结束-->
        <!--账号密码区域开始-->
        <div id="account">
            <div class="input">
                <input type="text" placeholder="登录帐号" name="username" required/>
            </div>
            <div class="input">
                <input type="password" placeholder="登录密码" name="password" required/>
            </div>
        </div>
        <!--账号密码区域结束-->
        <!--登录按钮开始-->
        <input type="submit" value="登录" class="login-btn"/>
        <!--登录按钮结束-->
    </form>
</div>
<!--登录区主体结束-->
</body>
</html>