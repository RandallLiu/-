var comm = {
    loadwait:function (){
        var proc = $('body');
        $(proc).block({
            message: '<i class="icon-spinner4 spinner"></i>',
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                'z-index':99999,
                backgroundColor: 'none'
            }
        });
    },
    loadunblock:function(){
        var proc = $('body');
        $(proc).unblock();
    },

    //发送get请求
    GET: function(options,callback){
        this.ajax(options,'get',callback);
    },
    //发送post请求
    POST: function(options,callback){
        this.ajax(options,'post',callback);
    },
    loadingShow:function(){
    },
    commonError:function(err){
        comm.dAlert(err);
    },
    ajaxError:function(){
        comm.dAlert('数据获取失败');
    },
    ajax: function(options,type,callback){
        var opts = {
            isCommonBefore: true, //默认是通用的加载中动画
            client: 1, //TODO这里应该根据内核判断
            isCors: false,//默认不跨域
            isLogin: false
        };

        $.extend(true, options, opts || {});

        //跨域，将请求地址 和 请求参数 都作为参数传递调用后台固定的跨域接口
        if (opts.isCors) {
            opts.data = {
                url: opts.url,//真实的url
                params: JSON.stringify(opts.data), //请求参数
            };
            //opts.url = URL.CORS;
        }

        if(options.url) opts.url = options.url;

        if(options.data) opts.data = options.data; else opts.data = [];

        opts.dt = (options.datatype == undefined || options.datatype === "json") ? "json" : "html"
        //添加默认的参数
        //opts.data.client = opts.client;
        //需要登录认证的请求
        if(opts.isLogin){ opts.data.token = comm.getToken() }
        //console.log(opts.dt);
        $.ajax({
            url: opts.url,
            data: opts.data,
            type: type,
            async: opts.async,
            dataType: opts.dt ,
            beforeSend: function() {
                //opts.isCommonBefore ? comm.loadwait() : (opts.beforeSend && opts.beforeSend())
            },
            success: function(json) {
                if(json && opts.dt === "json"){
                    if(callback){
                        if(Object.prototype.toString.call(json) === '[object Array]' || Object.prototype.toString.call(json) == '[object Object]')
                            callback(json);
                        else
                            callback(JSON.parse(json))
                    }else{
                        if(Object.prototype.toString.call(json) === '[object Array]' || Object.prototype.toString.call(json) == '[object Object]')
                            if(options.callback){
                                options.callback(json)
                            }else{
                                options.success(json)
                            }
                        else {
                            if(options.callback){
                                options.callback(JSON.parse(json))
                            }else{
                                options.success(JSON.parse(json))
                            }
                        }
                    }
                }else{
                    if(opts.dt == 'html'){
                        options.success((json))
                    }else {
                        comm.ajaxError();
                    }
                }
                comm.loadunblock();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                //console.log(XMLHttpRequest,XMLHttpRequest.status)
                console.error(opts.baseUrl || opts.url,' Request is error!',errorThrown);
                comm.loadunblock();
                // comm.commonError(XMLHttpRequest.responseText);
                console.log(XMLHttpRequest.status);
                // 重新登录
                if (XMLHttpRequest.status === 401) {
                    var gId = localStorage.getItem('groupGuid') , mid = localStorage.getItem('mGuid');
                    if ( mid ) {
                        $(`#${mid}`).modal('hide');
                    }
                    $('div.modal-backdrop:first').remove();
                    $("#alogin").click();
                }
                // 登录
                if (XMLHttpRequest.status === 410) {
                    $("#alogin").click();
                }
            },
            complete: function(XMLHttpRequest, textStatus) {
                //console.log(opts.baseUrl || opts.url,' ajax is complete');
                // console.log(XMLHttpRequest.status);
                // // 重新登录
                // if (XMLHttpRequest.status === 401) {
                //     $("#alogin").click();
                // }
                // // 登录
                // if (XMLHttpRequest.status === 410) {
                //     $("#alogin").click();
                // }
            },
            timeout: opts.timeout || 20000
        })
    },
    formload:function(frm,jsonStr){
        var obj = jsonStr;
        var key,value,tagName,type,arr;
        for(let x in obj){
            key = x;
            value = obj[x];
            frm.find("[name='"+key+"'],[name='"+key+"[]']").each(function(k,item){
                tagName = $(this)[0].tagName;
                type = $(this).attr('type');
                if(tagName=='INPUT'){
                    if(type=='radio'){
                        $(this).attr('checked',$(this).val()==value);
                    }else if(type=='checkbox'){
                        var ck = $(this);
                        if(Object.prototype.toString.call(value) === '[object Array]'){
                            $.each(value,function(k,v){
                                if(Object.prototype.toString.call(v) == '[object Object]'){
                                    if(v.id != undefined){
                                        if(v.id == ck.val()){
                                            ck.attr('checked', true);return;
                                        }
                                    }
                                    if(v.name != undefined){
                                        if(v.name == ck.val()){
                                            ck.attr('checked', true);return;
                                        }
                                    }
                                } else {
                                    if(v == ck.val()) {
                                        ck.attr('checked', true);
                                        return;
                                    }
                                }

                            });
                        }else {
                            arr = value.split(',');
                            for (var i = 0; i < arr.length; i++) {
                                if ($(this).val() == arr[i]) {
                                    $(this).attr('checked', true);
                                    break;
                                }
                            }
                        }
                    }else{
                        $(this).val(value);
                    }
                }else if(tagName=='SELECT' || tagName=='TEXTAREA'){
                    var strValue = ( Object.prototype.toString.call(value) === '[object Array]' ) ? value[k] : value
                    $(this).val(strValue) && $(this).val(strValue).trigger('change');
                }

            });
        }
    },

    unique: function(array){
        var n = {}, r = [], len = array.length, val, type;
        for (var i = 0; i < array.length; i++) {
            val = array[i];
            type = typeof val;
            if (!n[val]) {
                n[val] = [type];
                r.push(val);
            } else if (n[val].indexOf(type) < 0) {
                n[val].push(type);
                r.push(val);
            }
        }
        return r;
    },
    singleSelected:function(dt,elm){
        $(elm).on('click','tr',function(){
            var row = dt.$('tr.success');row.removeClass('success') && $(this).addClass('success');
            var ckb = dt.$('tr.success').find('td>input[type=radio],td>input[type=checkbox]'),input_type = ckb.attr('type');
            input_type === 'radio' ? ( ckb[0].checked = true ) : (input_type === 'checkbox' ? ckb[0].checked = !ckb[0].checked : '')
        });
    },

    multipleSelected:function ( dt,elm ) {
        $(elm).on('click', 'tr', function () {
            $(this).toggleClass('success');
            var isCK = $(this).find('td>input[type=checkbox]')[0].checked;
            $(this).find('td>input[type=checkbox]')[0].checked = !isCK;
        });
    },
    pAlert:function(text){
        new PNotify({
            addclass: 'alert-styled-left alert-arrow-left text-sky-royal blue-300',
            title: '提示!',
            text:text,
            type: 'success'
        });
    },
    dAlert:function(text){
        new PNotify({
            title: '警告!',
            text:text,
            type: 'error',
            addclass: 'alert-styled-left alert-arrow-left text-sky-royal'
        });
    },
    Alert:function(text,s){
        var g = (s===undefined||s) ? true : false;
        new PNotify({
            title: g ?'提示!':'警告!',
            text:text,
            type: g?'success':'error',
            addclass: g?('alert-styled-left alert-arrow-left text-sky-royal blue-300'):('alert-styled-left alert-arrow-left text-sky-royal')
        });
    },
    confirm: function(options) {
        bootbox.confirm({
            message: options.msg ? options.msg: '确认操作?',
            buttons: {
                confirm: {
                    label: options.btnOK ? options.btnOK: '确认',
                    className: options.classOK ? options.classOK: 'btn-success'
                },
                cancel: {
                    label: options.btnCancel ? options.btnCancel: '取消',
                    className: options.classCancel ? options.classCancel: 'btn-danger'
                }
            },
            callback: function(resp) {
                if (resp) options.callback(resp)
            }
        })
    },
    prompt: function(options) {
        var OPTS = {
            title: options.msg ? options.msg: '请输入有效值',
            inputType: options.type ? options.type: 'text',
            buttons: {
                confirm: {
                    label: options.btnOK ? options.btnOK: '确认',
                    className: options.classOK ? options.classOK: 'btn-success'
                },
                cancel: {
                    label: options.btnCancel ? options.btnCancel: '取消',
                    className: options.classCancel ? options.classCancel: 'btn-danger'
                }
            },
            callback: function(resp) {
                if (resp) {
                    options.callback(resp)
                }else{
                    if(options.error)
                        comm.dAlert(options.msg ? options.msg: '请输入有效值');
                }
            }
        };
        switch (options.type) {
            case 'password':
                OPTS.onEscape = false;
                break;
            case 'select':
                OPTS.inputOptions = options.inputs;
                break;
            case 'number':
                OPTS.inputOptions = options.number;
                break;
            default:
                break
        }
        bootbox.prompt(OPTS)
    },
    iNum:function(){
        return (/[\d.]/.test(String.fromCharCode(event.keyCode)));
    },
    fMoney:function(s,type){
        if (/[^0-9\.]/.test(s))
            return "0";
        if (s == null || s == "")
            return "0";
        s = s.toString().replace(/^(\d*)$/, "$1.");
        s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
        s = s.replace(".", ",");
        var re = /(\d)(\d{3},)/;
        while (re.test(s))
            s = s.replace(re, "$1,$2");
        s = s.replace(/,(\d\d)$/, ".$1");
        if (type == 0) {// 不带小数位(默认是有小数位)
            var a = s.split(".");
            if (a[1] == "00") {
                s = a[0];
            }
        }
        return s;
    },

    ellipsis:function(text,tip,width,dir){
        if(width == undefined || width == 0)
            width = 145;
        return `<div style="max-width:${width}px;overflow:hidden;text-overflow: ellipsis;" data-trigger="hover" data-popup="tooltip-ellipsis" data-placement="${dir?dir:'right'}" title="${tip?tip:''}">${(text ? text : '--')}</div>`;
    },
    word_break:function(text,width){
        if(width == undefined || width == 0)
            width = 145;
        return '<div style="white-space: normal;max-width:'+ width +'px;">'+(text ? text : '--')+'</div>';
    },
    thumbs:function(str){
        if(!str) return '';
        return str.substring(0,str.lastIndexOf('/')) +'_thumbs1'+ str.substring(str.lastIndexOf('/'),str.length);
    },
    tfoot:function(api,column,s){
        var intVal = function ( i) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            },
            total = api
                .column( column )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0),

            pageTotal = api
                .column( column, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
        $(api.column( column ).footer() ).html(comm.fMoney(pageTotal,s));
    },
    visibleColumn:function(dt,index,visible){
        if(Object.prototype.toString.call(index) === '[object Array]'){
            $.each(index,function(k,v){
                dt.api().column(v).visible(visible);
            });
        }else{
            dt.api().column(index).visible(visible);
        }
    },
    sfn : function() {
        var value = $(this).val();
        if (value == '') {
            $(this).data("prev", $(this).val());
            return;
        }
        var max = $(this).attr("maxlength");
        if (value.length > max)
            $(this).val(value.slice(0, max));
        var regex = /^\d+$/;
        if (!regex.test(value)) {
            $(this).val($(this).data("prev"));
        }
        $(this).data("prev", $(this).val());
    },
    jumpPage:function(container){

    },

    valid:function(option){
        option.ele.validate({
            errorClass: 'validation-error',
            successClass: 'validation-valid-label',
            highlight: function(element, errorClass) {
                var msg = $(element).attr('error');
                $(element).addClass(errorClass).attr('placeholder',( msg ? msg : '' ) + '不能为空');
            },
            unhighlight: function(element, errorClass) {
                $(element).removeClass(errorClass) && $(element).removeAttrs('placeholder');
            },
            errorPlacement: function(error, element) {
                //error.insertAfter(element);
            },
            submitHandler: function(form){
                if(option.fun)
                    option.fun();
                if(option.callback)
                    option.callback();
            }
        });
    },

    dt:function(options){
        var opts = {
            processing: options.processing?options.processing:true,
            bServerSide: true,
            sProcessing:"数据加载中...",
            iDisplayLength:options.size?options.size:15,
            searching : false,
            bFilter: false,
            paging : !options.paging ? options.paging : true,
            bInfo : ((options.bInfo === undefined || options.bInfo)?true:false),
            renderer: "bootstrap",
            bLengthChange: false,
            //sEmptyTable: "表中数据为空",
            //sZeroRecords:"表中数据为空",
            bAutoWidth: (options.width ? true : false),
            scrollCollapse:options.scrollCollapse ? options.scrollCollapse : false,
            bSort:options.sort?options.sort:false,
            order:options.order?options.order:[],
            sAjaxSource:options.url,
            scrollX: true,
            scrollY: options.scrollY ? options.scrollY : false,
        };

        if(options.pagingType){
            opts.pagingType = options.pagingType;
            opts.language = { paginate: {'next': '下一页 &rarr;', 'previous': '&larr; 前一页'}}
        }

        if(options.fixedColumns){
            opts.fixedColumns = options.fixedColumns;
        }

        var columns = [];

        if(options.columns){
            $.each(options.columns,function(k,v){
                if ( Object.prototype.toString.call(v) === '[object Object]' ) {
                    columns.push(v);
                }else {
                    columns.push({data: v});
                }
            });
            opts.columns = columns;
        }

        if(options.sy) opts.sScrollY = document.body.scrollHeight - 350;

        var dfs = [];
        if(options.c || options.columnDefs ){
            $.each(options.c || options.columnDefs,function(k,v){
                dfs.push({
                    aTargets: (v.idx || v.aTargets ) ? (v.idx || v.aTargets ) : v.i,
                    mRender: function(data,type,full){
                        return (v.fun) ? v.fun(data,full) : v.mRender(data,full);
                    },
                    orderable: ( v.orderable && v.orderable != undefined ) ? v.orderable : false
                });
            });
            opts.aoColumnDefs = dfs;
        }

        if(options.jump){
            opts.drawCallback = function(settings,callback){
                var page = dts.api().page.info().page;
                if (dts.api().page.info().pages > 1) {
                    $(".dataTables_paginate").append('<span class="jump-page" style="float: right;margin-right: 18px; margin-top: 6px;">跳到 <input style="width: 45px;text-align: center;" id="jump_page" min="1" onkeypress="return comm.iNum()" value="'+ (page + 1) +'"> 页</span>');
                }
                if(callback) callback();
            };

            $("body").delegate('#jump_page', 'keyup', function(event) {
                var page = Number($(this).val());
                if (event.keyCode == 13 && page > 0) {
                    dts.api().page(page - 1).draw(false);
                }
            });
        }

        if(options.fnck){
            opts.fnRowCallback = function(nRow, aData, iDisplayIndex){
                return options.fnck(nRow, aData, iDisplayIndex);
            }
        }

        if(options.preDrawCallback) {
            opts.preDrawCallback = function(setting){
                return options.preDrawCallback(setting);
            }
        }

        //if ( options.drawCallback )
        {
            opts.drawCallback = function ( settings ) {
                $('[data-popup=tooltip-ellipsis]').tooltip();
                setTimeout( ()=> { $('.dataTables_scrollBody').css('height','');},20);
                if ( options.drawCallback ) {
                    return options.drawCallback(settings);
                }
            }
        }

        var dts = options.ele.dataTable(opts);
        //选择行
        if(!options.s){
            comm.singleSelected(dts, options.ele.find('tbody'));
        }
        else {
            comm.multipleSelected(dts , options.ele.find('tbody') )
        }

        return dts;
    },

    bytesToSize : function (bytes) {
        if (bytes === 0) return '0 B';
        var k = 1024,
            sizes = ['B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toPrecision(2) + ' ' + sizes[i];
    },

    clear : function (form){
        var tag,type;
        $.each(form,function (k,item){
            tag = $(this)[0].tagName;
            type = $(this).attr('type');

            if(tag === 'INPUT'){
                if(type === 'radio' || type === 'checkbox')
                    $(this).attr('checked',false);
                else
                    $(this).val('');
            }
            if(tag==='SELECT' || tag==='TEXTAREA'){
                $(this).val('') && $(this).val('').trigger('change');
            }
        });
    },

    guid:function(){
        function suid(){
            return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
        }
        return (suid()+suid()+suid()+suid()+suid()+suid()+suid()+suid());
    },
    sec_to_time : function(s) {
        var t;
        if(s > -1){
            var hour = Math.floor(s/3600);
            var min = Math.floor(s/60) % 60;
            var sec = s % 60;
            if(hour < 10) {
                t = '0'+ hour + ":";
            } else {
                t = hour + ":";
            }

            if(min < 10){t += "0";}
            t += min + ":";
            if(sec < 10){t += "0";}
            t += sec.toFixed(0);
        }
        return t;
    },
    queryString : function(name) {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    },
    import_script:function(script_path,script_js){
        var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
        for(var i = 0 ; i < script_js.length; i++){
            var oscript  = document.createElement("script");
            oscript.src = script_path + script_js[i];
            oscript.type = 'text/javascript';
            head.appendChild(oscript);
        }
    },

    encrypt: function(s){
        var public_key = '-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC10U4RaCBZwfqlQkpjWv8L2CAeOd4Sx2gfpNqunPYwhofAE7H10w7wXraTgiToJRUfI5KZ/5SQ4b3MzIL9rlOgYMKyabVlf2+v49RHoIL/lNsFttV0HxF8AokfSj734SzarDBqScK/fy9mp/Nn78S6AcFJdMhjk3oql+mkwYFn1wIDAQAB-----END PUBLIC KEY-----';
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey(public_key);
        return encrypt.encrypt(s);
    },

    dateToUnix: function(string) {
        var f = string.split(' ', 2);
        var d = (f[0] ? f[0] : '').split('-', 3);
        var t = (f[1] ? f[1] : '').split(':', 3);
        return (new Date(
            parseInt(d[0], 10) || null,
            (parseInt(d[1], 10) || 1) - 1,
            parseInt(d[2], 10) || null,
            parseInt(t[0], 10) || null,
            parseInt(t[1], 10) || null,
            parseInt(t[2], 10) || null
        )).getTime() / 1000;
    },

    unixToDate: function(unixTime, isFull, timeZone) {
        if (typeof (timeZone) == 'number')
        {
            unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
        }
        var time = new Date(unixTime * 1000);
        var ymdhis = "";
        ymdhis += time.getUTCFullYear() + "-";
        ymdhis += (time.getUTCMonth()+1) + "-";
        ymdhis += time.getUTCDate();
        if (isFull === true) {
            ymdhis += " " + time.getUTCHours() + ":";
            ymdhis += time.getUTCMinutes() + ":";
            ymdhis += time.getUTCSeconds();
        }
        return ymdhis;
    },

    confirmCTL(url,msg,call){
        var obj = Object.prototype.toString.call(url),btn = 'search';
        if(obj === '[object HTMLAnchorElement]'){
            var _this = (url);
            btn = $(_this).data('btn')
            url = _this.href;
        }
        this.confirm(
            {
                msg:msg ? msg: '确定删除行记录?',
                callback: function (res){
                    comm.GET({
                        url : url,
                        success : function (resp){
                            comm.Alert(resp.msg ? resp.msg : '操作成功!',resp.code);
                            if( resp.code ){
                                $('form').find('button.'+btn).click();
                                if( call ) call(resp.code);

                            }
                        }
                    })
                }
            });
        return false;
    },

    doSubmit(obj){
        $(obj).parent().prev('div').find('form').submit();
    },

    doPrompt(url,opts,call){
        this.prompt({
            msg : opts.title,
            type : opts.type,
            btnOK : opts.OK,
            classOK : opts.classOK,
            btnCancel : opts.btnCancel,
            callback : function (val) {
                if(val) {
                    comm.GET({
                        url: url,
                        data:{value:val},
                        callback:function(result){
                            comm.Alert(result.msg,result.code);
                            call(result);
                        }
                    });
                }
            }
        })
        return false;
    },

    strToHigh(str){
        return  (str).replace(/(\w)/g,function(a,b,c,d){return ((c>6&&c<18)||c>(str.length-1))?'*':a});
    },

    doRequest(url,data,call,datatype){
        var option = {}

        option.url = url

        option.data = data?data:{}

        option.datatype = datatype?datatype:'html'

        option.success = function ( resp ) {
            if ( call ) { call( resp )}
        }
        comm.POST(option)

        return false;
    },

    handleValid : function ( temp ) {
        var frm = $(temp).find('form');
        var url = $(frm).attr("action"),btn = $('.btn-submit').data('btn');
        var rules = $(frm).attr("rules");
        rules = rules?JSON.parse(rules):{}
        $(frm).validate({
            errorClass: 'validation-error',
            successClass: 'validation-valid-label',
            highlight: function (element, errorClass) {
                $(element).addClass('error');
                $(element).removeClass(errorClass);
            },
            unhighlight: function (element, errorClass) {
                $(element).removeClass('error');
                $(element).removeClass(errorClass);
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element, this);

            },
            rules: rules.rules,
            messages: rules.messages ? rules.messages : {},
            submitHandler: function (form) {
                var data = $(frm).serialize();
                comm.POST({
                    url: url,
                    data: data,
                    success: function (resp) {
                        comm.Alert(resp.msg ? resp.msg : '操作成功!', resp.code);
                        if (resp.code) {
                            if (btn) $('form').find('.' + btn).click();
                            else $('form').find('button.search,button.btn_search').click();
                            temp.modal('hide');
                            var fn = $(frm).data('load');
                            if ( fn ) comm.page_reload();
                        }
                        comm.loadunblock();
                    },
                    error: function (resp) {
                        comm.Alert(resp.msg, false);
                        comm.loadunblock();
                    }
                });
            }
        });
    },

    doChoice : function ( dt ) {
        if (dt) {
            var data = dt.api().row('.success').data();
            if (data) {
                $('#mSelect').modal('hide');
            }
            return data;
        } else {
            $('#mSelect').modal('hide');
        }
    },

    closeModal: function () {
        var mId = localStorage.getItem('mGuid') , gId = localStorage.getItem('groupGuid');
        if ( gId ) {
            $(`#${gId}`).modal('hide');
            setTimeout(()=>{
                $(`#${gId}`).remove();
                localStorage.removeItem('groupGuid');
            },1000)
        }
        else {
            $(`#${mId}`).modal('hide');
            setTimeout(()=>{
                $(`#${mId}`).remove();
                localStorage.removeItem('mGuid');
            },1000)
        }
    },

    dictionary : function ( name , value , css) {
        if(!value)return '--';
        var cd=name[value];
        return cd ==null?value:((css && cd.css)?(`<span class="${cd.css}">${cd.name}</span>`):cd.name);
    },

    sModal(url, options,_this){
        $.ajaxSetup({async:false});
        var view = '';
        if ( url ) comm.GET({url: url, datatype: options.dt, success: function (resp) { view = resp.view ? resp.view : resp;}});

        if (!view) return false;
        var guid = comm.guid() , mGuid = localStorage.getItem('mGuid');
        if ( options.group ) {
            guid = options.group + '_' + guid;
            var groupGuid = localStorage.getItem('groupGuid');
            $(`#${groupGuid}`).modal('hide').remove();
            localStorage.setItem('groupGuid', guid);
        } else {
            $(`#${mGuid}`).modal('hide').remove();//$(`#${mGuid}`).remove();
            localStorage.setItem('mGuid', guid);
        }
        var title_name = ((options && options.title) ? options.title : '');

        var c = document.documentElement.clientHeight;

        if (c == 0) c = document.body.clientHeight;

        var buttons_string = $(_this).data('buttons') , buttons_json = {} ,buttons = ''  ;

        if ( buttons_string ) {
            if (Object.prototype.toString.call(buttons_string) == '[object String]' ) {
                buttons_json = JSON.parse(buttons_string);
            }
            buttons_json = buttons_string;
            $.each(buttons_json, function ( k , v ) {
                var attrs = "";
                if ( v.attrs ) {
                    $.each(v.attrs,function ( eleKey , eleVal ) {
                        attrs += ( eleKey + '= "' + eleVal +'"' );
                    });
                }
                buttons += `<${v.tag?v.tag:'botton'} ${attrs} class="btn ${v.cssClass}"> ${v.text} </${v.tag?v.tag:'botton'}>`;
            });
        } else {
            buttons = '<button type="button"  class="btn btn-success btn-submit" data-btn="'+(options.Btn ? options.Btn : '')+'" style="'+ ((options && (options.Yes === undefined || options.Yes === 'Y')) ? '': 'display:none')+'"> '+(options.Text?options.Text:'确定')+'</button>';
        }

        var templet = '<div id="'+ guid +'" class="modal fade">' +
            '<div class="modal-dialog modal-'+ ((options && options.size)?(options.size):'')+'">'+
            '    <div class="modal-content">'+
            '        <div class="modal-header '+ ((options && options.bg) ? options.bg : '') +'">'+
            '            <button type="button" class="close">&times;</button>'+
            '            <h5 class="modal-title">' +(title_name?title_name:'--') +'</h5>'+
            '        </div>'+
            '        <div class="modal-body" style="'+ ((options.H == undefined) ? ('max-height:'+ (c-165) +'px;') : ('height:' + (c-150) + 'px;')) +' overflow:auto"> '+
            view +
            '        </div>'+
            '        <div class="modal-footer">'+
            '             <button type="button" class="btn btn-link btn_close">关闭</button>'+
            buttons +
            '        </div>'+
            '    </div>'+
            '</div>'+
            '</div>';
        $('body').append(templet);

        var mModal = $(`#${guid}`);

        $(`#${guid} .btn-submit`).unbind().on('click',function (e) {
            e.preventDefault();e.stopPropagation();
            return comm.doSubmit(this)
        });

        $(`#${guid} .btn_close,.close`).on('click',function (e) {
            e.preventDefault();
            e.stopPropagation();
            mModal.modal('hide');
        });
        mModal.modal('show');
        comm.handleValid( mModal , _this );
    },

    selectModal(url, options){
        $('#mSelect').modal('hide').remove();
        var name = (options && options.title) ? options.title : '',view = '';
        $.ajaxSetup({async:false});
        if ( url.length > 5 ) {
            comm.GET({
                url: url, datatype: options.dt, success: function (resp) {
                    if (resp.view)
                        view = resp.view;
                    else
                        view = resp;
                }
            });
        }

        var c = document.documentElement.clientHeight;
        if (c == 0) c = document.body.clientHeight;
        var templet = `<div id="mSelect" class="modal fade">
                            <div class="modal-dialog ${(options && options.size)?('modal-' + options.size):''}">
                                <div class="modal-content">
                                    <div class="modal-header ${(options && options.bg) ? options.bg : ''}">
                                        <button type="button" class="close" onclick="comm.doChoice()">&times;</button>
                                        <h5 class="modal-title">${name?name:'--'}</h5>
                                    </div>
                                    <div class="modal-body" style="${(options.H == undefined) ? 'max-height:650px;' : 'height:' + (c-180) + 'px;'} overflow:auto">
                                        ${view}
                                    </div>
                                    <div class="modal-footer">
                                         <button type="button" class="btn btn-link" onclick="comm.doChoice()">关闭</button>
                                         <button type="button"  class="btn btn-success btnChoice" data-btn="${ options.Btn ? options.Btn : ''}" style="${(options && (options.Yes == undefined || options.Yes == 'Y')) ? '': 'display:none'}"> ${(options.Text?options.Text:'确定选择')} </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
        $(templet).find('button.btn-submit').on('click',function (){});
        $(templet).modal('show');
    },

    // 批量操作
    batchCtl:function (url,msg) {
        console.log(url);
        var ids = [],ckb = $('[name=sck]:checked');
        msg = msg ? msg : '确定批量操作?'
        if( !ckb.length ) {comm.Alert('请选择操作行记录',false);return false;}

        ckb.each(function (k,item){ids.push(item.value);});
        // return false;
        comm.confirmCTL((url + '?id=' + ids.join(',') ), msg);
        return false;
    },

    // 获取本地存储信息
    getStorage:function (key) {
        return localStorage.getItem(key)
    },

    // 获取省分ID
    PROID:function (){
        const provinceId = comm.getStorage('PROVINCEID');
        return provinceId?provinceId:11;
    },
    // 获取省分名称
    PROVINCE:function (pid) {
        return province_data[pid?pid:11];
    },
    time : 60,
    countdown : function ( btn , envent ) {
        if ( comm.time === 60 ){
            var timer = setInterval(function () {
                if (comm.time === 0) {
                    // 清除定时器和复原按钮
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.innerHTML = '获取验证码';
                    comm.time = 60;
                } else {
                    btn.innerHTML = '剩余' + comm.time + '秒';
                    comm.time--;
                }
            }, 1000);
            if (envent) envent();
        }
    },
    page_reload:function (){ setTimeout(()=>{window.location.reload()},3000)},
    heartbeat : function () {
        $.ajaxSetup({async:false});var is_login = false;
        comm.GET({url : '/heartbeat',success:function (resp){is_login = resp.code}});
        return is_login;
    },
    toLogin : function () {
        console.log($('div.modal-backdrop'));
        $("#alogin").click(); $('div.modal-backdrop:first').remove()
    }
};

$(function (){
    $('.aprovince i').text(comm.PROVINCE());
    setInterval(()=>{
        // 弹出层
        $('.sModal,.hModal').unbind().on('click',function (e){e.preventDefault();set_modal(this);return false;});

        $('.selModal').unbind().on('click',function (e){
            e.preventDefault();comm.selectModal(this.href,{title:this.lang ? this.lang : $(this).text(), bg : $(this).data('bg') , size:$(this).data('size'), Yes : $(this).data('yes') ,H : $(this).data('h'),dt: "html",Btn:$(this).data('btn'),Text:$(this).data('text')});
            return false;
        });

        //
        $('select[data-auth=login],select[data-auth=vip]').unbind().on("click",function (e){
            e.preventDefault(); var _this = this;
            var is_login = comm.heartbeat()
            if ($(_this).data('auth') == 'login' && !is_login) comm.toLogin();
        })
    },1000);
    $('.hModal').unbind().on('click',function (e){
        e.preventDefault();
        set_modal(this);
        return false;
    });
    // 设置下拉框
    $('select.select').select2();
    // 设置下拉框(无搜索)
    $('select.select[role=min]').select2({minimumResultsForSearch: "-1" });
    // 导出Excel
    $('.ExExcel').on('click',function (e) {
        e.defaultPrevented;
        comm.loadwait();
        window.location = this.href + '?' + $($(this).attr('frm')).serialize();
        comm.loadunblock();
        return false;
    });

    // 全选
    $('input[name=ckall],input[name=ck_all]').on('click',function () {
        $('[name=sck]:checkbox,[name=ck_location]:checkbox').prop('checked',this.checked);
    });

    // 批量删除
    $('a[name=dall]').on('click',function () {
        var ids = [],ckb = $('[name=sck]:checked'),url = this.href;
        if( !ckb.length ) {comm.Alert('请选择需要删除的行!',false);return false;}
        ckb.each(function (k,item){ids.push(item.value);});
        comm.confirmCTL((url + '?id=' + ids.join(',') ), '确定删除选择行?');
        return false;
    });

    function set_modal(obj){
        const _this = obj;
        comm.sModal(_this.href,{title:_this.lang ? _this.lang : $(_this).text(),group:$(_this).data('group'), bg : $(_this).data('bg') , size:$(_this).data('size'), Yes : $(_this).data('yes') ,H : $(_this).data('h'),dt: ($(_this).hasClass('hModal') ? "html" : "json"),Btn:$(_this).data('btn'),Text:$(_this).data('text')},_this);
    }
})
