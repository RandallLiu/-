;(function($) {
    $.fn.extend({
        toSubmit:function (opts){
            var obj = this;
            if(opts.error){
                var errorCallBack = opts.error;
                delete opts.error;
            }
            if(opts.success){
                var successCallBack = opts.success;
                delete opts.success;
            }

            if( opts.before ) {
                var beforeCallBack = opts.before;
                delete opts.before;
            }

            $(this).validate({
                errorClass: 'validation-error',
                successClass: 'validation-valid-label',
                highlight: function(element, errorClass) {
                    $(element).addClass('error');
                    $(element).removeClass(errorClass);
                },
                unhighlight: function(element, errorClass) {
                    $(element).removeClass('error');
                    $(element).removeClass(errorClass);
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element,this);

                },
                rules : opts.rules ? opts.rules : {},
                messages : opts.messages ? opts.messages : {},
                submitHandler: function(form){
                    if( beforeCallBack ) beforeCallBack()
                    var frm = $(form);
                    if(!opts.url) opts.url = frm.attr('action');
                    opts.data = frm.serialize();
                    if ( opts.url ) {
                        comm.POST({
                            url: opts.url,
                            data: opts.data,
                            success: function (resp) {
                                if (resp.code) {
                                    if (successCallBack) successCallBack(resp, form);
                                } else {
                                    if (errorCallBack) errorCallBack(resp);
                                }
                            }
                        });
                    }
                }
            }
            );
        }
    })
})(jQuery);
