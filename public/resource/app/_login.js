/**
 * Created by Radall on 18/11/26.
 */

$(function(){
    comm.import_script('/resource/app/',['jsent.min.js']);
    var vrm = $('.form-login');
    comm.valid({
        ele : vrm,
        fun : function(){
            comm.POST({
                url : '/sigin?_='+Math.random() + '&url=' + (comm.queryString('url') ? comm.queryString('url') : ''),
                data : {formId : vrm.find('input[name=formId]').val(), username : vrm.find('input[name=username]').val(), password : comm.encrypt(vrm.find('input[name=password]').val()),code  :vrm.find('input[name=code]').val(),ccod : vrm.find('input[name=ccod]').val()},
                callback:function(result){
                    console.log(result);
                    if(result.code){
                        window.location.href = result.url;
                    }else{
                        comm.dAlert(result.msg);
                    }
                }
            });
        }
    });
});