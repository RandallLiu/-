
$(function(){
    //$(".form-validate").validate(valid);
    comm.import_script('/resource/app/',['jsent.min.js']);

    var frm = $(".form-validate");

    comm.valid({
        ele: frm,
        fun:function(){
            //comm.loadwait();
            comm.POST({
                url : '/create_to_account',
                data : {
                    formId : frm.find('input[name=formId]').val(),
                    username : frm.find('input[name=username]').val(),
                    password : comm.encrypt(frm.find('input[name=password]').val()),
                    code  :frm.find('input[name=invcode]').val(),
                    rp : comm.encrypt(frm.find('input[name=repeat_password]').val()),
                    email:frm.find('input[name=email]').val(),
                    phone:frm.find('input[name=phone]').val()
                },
                success : function(result){
                    if(result.code){
                        comm.pAlert(result.msg);
                    }else{
                        comm.dAlert(result.msg);
                    }
                    //comm.loadunblock();
                }
            })
        }
    });
});