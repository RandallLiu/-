
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APPNAME?></title>
    <meta name="description" content="，高考，高考志愿，志愿填报，志愿填报查询">
    <meta name="keywords" content=",高考，高考志愿，志愿填报，志愿填报查询">

    <link href="/resource/assets/css/icons/icomoon/styles.css?_=2.0" rel="stylesheet" type="text/css">
    <?=link_tag('resource/assets/css/datatable-extend.css')?>
    <?=script_tag('resource/assets/js/plugins/loaders/blockui.min.js')?>

    <script type="text/javascript" src="/resource/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/plugins/loaders/blockui.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/plugins/forms/validation/validate.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/plugins/forms/validation/localization/messages_zh.js"></script>
    <?=script_tag('resource/assets/js/plugins/forms/selects/select2.min.js')?>
    <script type="text/javascript" src="/resource/assets/js/plugins/notifications/pnotify.min.js"></script>
    <script type="text/javascript" src="/resource/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/resource/assets/js/core/app.js"></script>
    <?=script_tag('resource/app/form_validation.js')?>
    <script type="text/javascript" src="/resource/app/comm.js?_=<?php echo time()?>"></script>
</head>
<body style="background: url(/resource/assets/images/login-bg.de77ac6f.jpg) repeat;">
<!-- Page container -->
<div class="page-container login-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content">
                <!-- Simple login form -->
                <form class="form-login" action="/v2/comm/login">
                    <div class="panel panel-body login-form">
                        <input name="login_type" type="hidden" value="passwd">
                        <input name="url" type="hidden" value="<?=($_REQUEST["url"]?:'')?>">
                        <div class="text-center">
                            <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
                            <h5 class="content-group"><?=APPNAME?>·登录</h5>
                        </div>

                        <div class="form-group sms" style="display: none">
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="手机号码" name="phone" maxlength="11">
                                <a type="button" class="input-group-addon btn_binder_send_sms">
                                    <span class="sms" style="display: none">
                                        获取验证码
                                    </span>
                                </a>
                            </div>
                        </div>


                        <div class="form-group has-feedback has-feedback-left passwd">
                            <div class="form-control-feedback">
                                <i class="icon-user text-muted"></i>
                            </div>
                            <input class="form-control" placeholder="手机号码" name="passwd_phone" maxlength="11" required>
                        </div>


                        <div class="form-group has-feedback has-feedback-left passwd">
                            <div class="form-control-feedback">
                                <i class="icon-user text-muted"></i>
                            </div>
                            <input class="form-control" placeholder="登录密码" name="passwd" type="password" required>
                        </div>

                        <div class="row sms" style="display: none">
                            <div class="col-md-7">
                                <div class="form-group has-feedback has-feedback-left">
                                    <div class="form-control-feedback">
                                        <i class="icon-spell-check text-muted"></i>
                                    </div>
                                    <input type="text" class="form-control" size="4" maxlength="4" placeholder="输入 图片验证码" name="captcha">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <img src="/home/captcha?_=<?=time()?>" onclick="this.src='/home/captcha?_=<?=time()?>'">
                                </div>
                            </div>
                        </div>

                        <div class="form-group has-feedback has-feedback-left sms" style="display: none">
                            <div class="form-control-feedback">
                                <i class="icon-user text-muted"></i>
                            </div>
                            <input class="form-control" placeholder="请输入 短信验证码" name="code" maxlength="6">
                        </div>

                        <div class="text-right tabch">
                            <a href="#" data-text="passwd" class="passwd">短信登录</a>
                            <a href="#" data-text="sms" style="display: none" class="sms">密码登录</a>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">登录 <i class="icon-circle-right2 position-right"></i></button>
                        </div>
<!--                        <div class="text-center"><a href="/home/register">注册</a></div>-->
                    </div>
                </form>
                <!-- /simple login form -->
            </div>
            <!-- /content area -->
        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->
</div>

<script>
    $('.form-login').toSubmit({
        success : function (resp) {
            console.log(resp);
            window.location.href = resp.url;///comm.queryString('url') ? comm.queryString('url'):'/';
        },
        error:(resp)=>{
            comm.dAlert(resp.msg,false);
        }
    })

    $(".tabch a").on("click",function (){
        var v = $(this).data("text") ,passwd = $('.passwd'),sms = $('.sms') ;
        if (v==='passwd') {
            $('input[name=login_type]').val("sms")
            passwd.css("display","none").find("input"); sms.css("display","");
            passwd.find("input").removeAttr("required");sms.find("input").attr("required",true);
        } else {
            passwd.css("display",""); sms.css("display","none");sms.find("input").removeAttr("required");
            passwd.find("input").attr("required",true);
            $('input[name=login_type]').val("passwd")
        }
    });

    var btn_binder_sms = document.querySelector('.btn_binder_send_sms');
    btn_binder_sms.addEventListener('click', function() {
        var reg = (/^1[3456789]{1}\d{9}$/) , phone = $('.form-login input[name=phone]').val() , captcha = $('.form-login input[name=captcha]').val();
        if ( !captcha ) {comm.Alert('请输入图片字符验证码',false);return false;}
        if ( !reg.test(phone) ) {comm.Alert('请输入正确的手机号',false); return false;}
        comm.countdown( btn_binder_sms , function () {
            comm.doRequest('/v2/comm/send_sms/login',{phone : phone , captcha : $('.form-login input[name=captcha]').val() },(resp)=>{comm.Alert(resp.msg,resp.code)},'json');
        });
    });

</script>
</body>
</html>