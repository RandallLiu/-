<form class="form-horizontal login" action="/v2/comm/login" data-load="true">
    <input name="login_type" value="passwd" type="hidden">
    <div class="form-group">
        <label class="control-label col-lg-2">手机号码<span class="text-danger">*</span> </label>

        <div class="col-lg-10 sms" style="display: none">
            <div class="input-group">
                <input class="form-control" placeholder="手机号码" name="phone" maxlength="11">
                <a type="button" class="input-group-addon btn_binder_send_sms">
                    <span class="sms" style="display: none">
                        获取验证码
                    </span>
                </a>
            </div>
        </div>

        <div class="col-lg-10 passwd">
            <input class="form-control" placeholder="手机号码" name="passwd_phone" maxlength="11">
        </div>
    </div>

    <div class="form-group passwd">
        <label class="control-label col-lg-2">登录密码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="登录密码" name="passwd" type="password" >
        </div>
    </div>


    <div class="form-group sms" style="display: none">
        <label class="control-label col-lg-2">图片验证码 <span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <div class="input-group">
                <input class="form-control" placeholder="请输入 图片验证码" name="captcha" maxlength="4">
                <a type="button" class="input-group-addon" style="padding: 0px"><img src="/home/captcha?_=<?=time()?>" onclick="this.src='/home/captcha?_=<?=time()?>'" style="height: 33px;"></a>
            </div>
        </div>
    </div>

    <div class="form-group sms" style="display: none">
        <label class="control-label col-lg-2">短信验证码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="请输入 短信验证码" name="code" maxlength="6">
        </div>
    </div>

    <div class="text-right tabch">
        <a href="#" data-text="passwd" class="passwd">短信登录</a>
        <a href="#" data-text="sms" style="display: none" class="sms">密码登录</a>
    </div>
</form>

<script>

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
        var reg = (/^1[3456789]{1}\d{9}$/) , phone = $('.login input[name=phone]').val() , captcha = $('.login input[name=captcha]').val();
        if ( !captcha ) {comm.Alert('请输入图片字符验证码',false);return false;}
        if ( !reg.test(phone) ) {comm.Alert('请输入正确的手机号',false); return false;}
        comm.countdown( btn_binder_sms , function () {
            comm.doRequest('/v2/comm/send_sms/login',{phone : phone , captcha : $('.login input[name=captcha]').val() },(resp)=>{comm.Alert(resp.msg,resp.code)},'json');
        });
    });
</script>