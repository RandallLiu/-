/*global fileProgress */
var delParent;
;(function($) {
    $.fn.extend({
        qiniuImageup:function(options){
            var obj = $(this);
            var isMax = true;
            if (typeof options != "object") {
                comm.dAlert('参数错误!');
                return;
            }

            options.imageNum = options.imageNum ? options.imageNum: 9;

            options.fit = options.fit ? options.fit : ["jpg","png","bmp","jpeg","pdf","doc","txt","gif","zip","mp4","xls","xlsx","mov"];

            if(options.filesAdded){
                var filesAddedCallBack = options.filesAdded;
                delete options.filesAdded

            }

            if(options.beforeUpload){
                var beforeUploadCallBack = options.beforeUpload;
                delete options.beforeUpload;
            }

            if(options.progress){
                var progressCallBack = options.progress;
                delete options.progress;
            }

            if(options.fileUploaded){
                var uploadedCallBack = options.fileUploaded;
                delete options.fileUploaded;
            }

            if(options.complete){
                var complete = options.complete;
                delete options.complete;
            }

            var opts = {
                runtimes: 'html5,flash,html4',
                browse_button: obj.attr('id'),
                container: options.container,
                drop_element: options.container,
                max_file_size: '200mb',
                flash_swf_url: 'bower_components/plupload/js/Moxie.swf',
                dragdrop: true,
                chunk_size: '4mb',
                multi_selection: !(mOxie.Env.OS.toLowerCase()==="ios"),
                uptoken_url: options.upk?options.upk:'/qiniu/ybucket',
                domain: options.domain?options.domain:'img.lizhizao.com.qiniudns.com',
                get_new_uptoken: false,
                auto_start: true,
                log_level: 5,
                init: {
                    FilesAdded: function(up, files) {
                        var imgContainer = $(obj).parents(".z_photo");
                        var numUp = imgContainer.find(".up-section").length;
                        var totalNum = numUp + files.length; // 总的数量

                        if(files.length > options.imageNum || totalNum > options.imageNum) {
                            isMax = false;
                            comm.dAlert('上传文件数目不可以超过 '+ options.imageNum +' 个,请重新选择!');return;
                        }
                        plupload.each(files, function (file) {
                            if (!isMax) {
                                up.removeFile(file);return false;
                            } else {
                                var isVailes = isValid(file.name);

                                if (isVailes) {
                                    var progress = new FProgress(file, obj, options);

                                    progress.setText('等待上传<br />('+ plupload.formatSize(file.size).toUpperCase() +')', true);
                                } else {
                                    up.removeFile(file);
                                    comm.dAlert('文件格式不正确!');
                                    return false;
                                }
                            }
                        });

                        if(filesAddedCallBack) filesAddedCallBack(files);
                    },

                    BeforeUpload: function(up, file) {
                        var progress = new FProgress(file,obj,options);
                        var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                        if (up.runtime === 'html5' && chunk_size) {
                            //progress.setText(chunk_size,true);
                        }
                    },

                    UploadProgress: function(up, file) {
                        if(isMax) {
                            var progress = new FProgress(file, obj, options);
                            var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                            progress.setProgress(file.percent + "%", file.speed, chunk_size);
                        }else{
                            up.removeFile(file);
                        }
                        if(progressCallBack) progressCallBack(file);
                    },

                    UploadComplete: function(){
                        $('.moxie-shim-html5').hide();
                        if(complete) complete();
                    },

                    FileUploaded: function(up, file, info){
                        var progress = new FProgress(file,obj,options);
                        progress.setComplete(up, info);
                        if(uploadedCallBack) uploadedCallBack(file);
                    },

                    Key: function(up, file) {
                        // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                        var key = file.name.substring(0,1) + comm.guid() + '.' +  file.name.substring(file.name.lastIndexOf('.') + 1,file.name.length);
                        return key;
                    },

                    Error: function(up, err, errTip) {
                    }
                }
            };

            $(".z_photo").delegate(".close-upimg", "click", function(event) {
                event.preventDefault();
                event.stopPropagation();
                $(".works-mask").show();
                delParent = $(this).parent();
            });

            $(".wsdel-ok").click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                $(".works-mask").hide();
                var numUp = delParent.siblings(".up-section").length;
                if (numUp < options.imageNum + 1) {
                    delParent.parent().find(".z_file").show();
                }
                delParent.remove();
            });

            $(".wsdel-no").click(function() {
                $(".works-mask").hide();
            });



            var isValid = function(file) {
                var res, suffix = "";
                var imageSuffixes = options.fit;
                var suffixMatch = /\.([a-zA-Z0-9]+)(\?|\@|$)/;

                if (!file || !suffixMatch.test(file)) {
                    return false;
                }
                res = suffixMatch.exec(file);
                suffix = res[1].toLowerCase();
                for (var i = 0, l = imageSuffixes.length; i < l; i++) {
                    if (suffix === imageSuffixes[i]) {
                        return true;
                    }
                }
                return false;
            };

            return Qiniu.uploader(opts);
        }
    });
})(jQuery);

function FProgress(file,obj,options){
    this.domain = 'http://img.lizhizao.com/';
    this.fileProgressID = file.id;
    this.file = file;
    this.fileProgressWrapper = $('#' + this.fileProgressID);

    if (!this.fileProgressWrapper.length) {
        var section =
            '<section class="up-section fl loading" id="'+ this.fileProgressID +'">' +
            '   <span class="up-span"></span>' +
            '   <img class="close-upimg" src="/resource/assets/css/img/a7.png">' +
            '   <img class="up-img">' +
            '   <label class="img-name-p"></label>' +
            '   <input type="hidden" style="display:none;" name="'+ options.name +'">' +
            '</section>';

        obj.before(section);

        $("#" + this.fileProgressID).find(".close-upimg").on("click",function(event){
            event.preventDefault();
            event.stopPropagation();
            $(".works-mask").show();
            delParent = $(this).parent();
        });
    }
}

FProgress.prototype.setText = function(text,show){
    if(show){
        $('#'+this.fileProgressID).find('.img-name-p').html(text);
    }
};

FProgress.prototype.setProgress = function(percentage, speed, chunk_size){
    var file = this.file;
    var uploaded = file.loaded;
    var size = plupload.formatSize(uploaded).toUpperCase();
    var formatSpeed = plupload.formatSize(speed).toUpperCase();
    this.setText("已传:" + size + " ,速度:" + formatSpeed + "/s",true);
};

FProgress.prototype.setComplete = function(up, info){
    var res = $.parseJSON(info);
    var url,str;

    var imgaeView = '?imageView2/2/w/240/h/180/q/80';

    if(res.url) {
        url = res.url;
        str = '<a href="'+ url +'" target="_blank">查看文件</a>>';
    } else {
        var domain = this.domain;
        url = domain + encodeURI(res.key);
        var link = domain + res.key;
        str = "<a href=" + url + " target='_blank' >查看文件</a>";
    }

    var section = $('#' + this.fileProgressID);

    section.find('.up-span').html(str);

    section.find('.img-name-p').hide();



    section.removeClass('loading');

    var isImage = function(url) {
        var res, suffix = "";
        var imageSuffixes = ["png", "jpg", "jpeg", "gif", "bmp"];
        var suffixMatch = /\.([a-zA-Z0-9]+)(\?|\@|$)/;

        if (!url || !suffixMatch.test(url)) {
            return false;
        }
        res = suffixMatch.exec(url);
        suffix = res[1].toLowerCase();
        for (var i = 0, l = imageSuffixes.length; i < l; i++) {
            if (suffix === imageSuffixes[i]) {
                return true;
            }
        }
        return false;
    };

    var isImg = isImage(url);

    var ext = res.key.split('.')[1].toLocaleLowerCase();

    if(isImg) {
        section.find('img.up-img').attr('src', url + imgaeView);
    }else if(ext == 'pdf'){
        section.find('img.up-img').attr('src', '/resource/assets/css/img/pdf.png');
    }
    else{
        section.find('img.up-img').attr('src', '/resource/assets/css/img/files.png');
    }

    section.find('input').val(url);
};

