<form class="form-horizontal " id="frm_reg" action="/v2/comm/reg" data-load="true">
    <div class="form-group">
        <label class="col-lg-2  control-label">所在地区 : </label>
        <div class="col-lg-10"> <?=\App\Libraries\LibComp::select('PROVINCE',['name'=>'province','class' => 'select'],'',false)?> </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">考生姓名<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="请输入 考生姓名" name="name" required="required">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">手机号码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <div class="input-group">
                <input class="form-control" placeholder="注册手机号" name="phone" required="required" maxlength="11">
                <a type="button" class="input-group-addon btn_binder_send_sms">获取验证码</a>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">图片验证码 <span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <div class="input-group">
                <input class="form-control" placeholder="请输入 图片验证码" name="captcha" maxlength="4" required>
                <a type="button" class="input-group-addon" style="padding: 0px"><img src="/home/captcha?_=<?=time()?>" onclick="this.src='/home/captcha?_=<?=time()?>'" style="height: 33px;"></a>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">短信验证码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="请输入 短信验证码" name="code" required="required" maxlength="6">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">登录密码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="请输入 登录密码" name="password" required="required" minlength="6" type="password">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">确认密码<span class="text-danger">*</span> </label>
        <div class="col-lg-10">
            <input class="form-control" placeholder="请输入 确认密码" name="repasswd" required="required" minlength="6" type="password">
        </div>
    </div>

</form>

<script>
    $("#frm_reg select").select2({minimumResultsForSearch:-1});
    var btn_binder_sms = document.querySelector('.btn_binder_send_sms');
    btn_binder_sms.addEventListener('click', function() {
        var reg = (/^1[3456789]{1}\d{9}$/) , phone = $('#frm_reg input[name=phone]').val() , captcha = $('#frm_reg input[name=captcha]').val();
        if ( !captcha ) {comm.Alert('请输入图片字符验证码',false);return false;}
        if ( !reg.test(phone) ) {comm.Alert('请输入正确的手机号',false); return false;}
        comm.countdown( btn_binder_sms , function () {
            comm.doRequest('/v2/comm/send_sms/reg',{phone : phone , captcha : $('#frm_reg input[name=captcha]').val() },(resp)=>{comm.Alert(resp.msg,resp.code)},'json');
        });
    });
</script>
