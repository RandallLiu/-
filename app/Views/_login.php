
<!--
    Last modified: 2022-06-25 18:40:11
    Url: https://www.axui.cn
-->
<!DOCTYPE html>
<html>
<head>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="format-detection" content="email=no" />
    <meta name="wap-font-scale"  content="no" />
    <meta name="viewport" content="user-scalable=no, width=device-width" />
    <meta content="telephone=no" name="format-detection" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?=APPNAME?></title>
    <meta name="description" content="，高考，高考志愿，志愿填报，志愿填报查询">
    <meta name="keywords" content=",高考，高考志愿，志愿填报，志愿填报查询">

    <link href="/resource/ax/css/ax.css" rel="stylesheet" type="text/css" >
    <link href="/resource/ax/css/ax-response.css" rel="stylesheet" type="text/css" >
    <link href="/resource/ax/css/main.css" rel="stylesheet" type="text/css">
</head>

<body class="ax-align-origin">

<div class="login ax-shadow-cloud ax-radius-md">
    <div class="ax-row ax-radius-md ax-split">
        <div class="ax-col ax-col-14 ax-radius-left ax-radius-md cover"></div>
        <div class="ax-col ax-col-12">
            <div class="core">

                <div class="ax-break"></div>

                <div class="ax-tab" axTab>

                    <ul class="ax-row ax-tab-nav ax-menu-tab">
                        <a href="#" class="ax-item">登录账号</a>
                        <a href="#" class="ax-item">注册新用户</a>
                        <li class="ax-col"></li>

                    </ul>

                    <ul class="ax-tab-content">
                        <li>
                            <form>
                                <div class="ax-break"></div>
                                <div class="ax-break ax-hide-tel"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input"><span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span>
                                                <input name="username" value="admin" placeholder="输入 登录名称" type="text">
                                                <span class="ax-pos-right"><a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input"><span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-lock-f"></i></span>
                                                <input name="password" placeholder="输入密码" type="password"><span class="ax-pos-right"><a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <div class="ax-row">
                                                    <div class="ax-flex-block">
                                                        <span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-shield-f"></i></span>
                                                        <input name="username" placeholder="输入验证码..." value="" type="text"><span class="ax-pos-right"><a class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                                    </div>
                                                    <a class="ax-form-img"><img src="/home/captcha?_=<?=time()?>" onclick="this.src='/home/captcha?_=<?=time()?>'"></a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <div class="ax-row">
                                                    <div class="ax-flex-block"></div>
                                                    <a href="#" class="ax-form-txt ax-color-ignore">短信登录</a>
                                                    <a href="#" class="ax-form-txt ax-color-ignore">密码登录</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-flex-block">
                                            <div class="ax-form-input"><button type="button" class="ax-btn ax-primary ax-full">登录</button></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-break ax-hide-tel"></div>
                                <div class="ax-break ax-hide-tel"></div>

                            </form>
                        </li>

                        <li>
                            <form>
                                <div class="ax-break"></div>
                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">

                                                <?=\App\Libraries\LibComp::select('PROVINCE',['name'=>'province','class' => 'select'],'',false)?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span>
                                                <input name="username" placeholder="输入名称" type="text">
                                                <span class="ax-pos-right">
                                                    <a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span>
                                                <input name="username" placeholder="输入名称" type="text">
                                                <span class="ax-pos-right">
                                                    <a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span>
                                                <input name="username" placeholder="输入名称" type="text">
                                                <span class="ax-pos-right">
                                                    <a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <span class="ax-pos-left" style="width: 2.4rem;"><i class="ax-iconfont ax-icon-me-f"></i></span>
                                                <input name="username" placeholder="输入名称" type="text">
                                                <span class="ax-pos-right">
                                                    <a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input"><span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-lock-f"></i></span>
                                                <input name="password" placeholder="输入密码" type="password">
                                                <span class="ax-pos-right"><a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-form-con">
                                            <div class="ax-form-input">
                                                <span class="ax-pos-left" style="width:2.4rem;"><i class="ax-iconfont ax-icon-lock-f"></i></span>
                                                <input name="password" placeholder="再次输入密码" type="password"><span class="ax-pos-right">
                                                    <a href="#" class="ax-iconfont ax-icon-close ax-val-none"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="ax-break-md"></div>


                                <div class="ax-break-md"></div>

                                <div class="ax-form-group">
                                    <div class="ax-flex-row">
                                        <div class="ax-flex-block">
                                            <div class="ax-form-input"><button type="button" class="ax-btn ax-primary ax-full">注册</button></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ax-break"></div>
                                <div class="ax-break ax-hide-tel"></div>
                                <div class="ax-break ax-hide-tel"></div>

                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!--正文结束-->

<div class="footer"></div>

<script src="/resource/ax/js/ax.min.js" type="text/javascript"></script>

<script>

</script>
</body>

</html>