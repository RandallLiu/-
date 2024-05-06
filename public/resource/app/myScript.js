/*
 系统基本脚本库
 */
var yds = {
    root: "/mini",
    //网站形式，0虚拟目录/1域名,
    siteType: 0,
    //请调用本方法
    getRootPath: function ()
    {
        ///	<summary>
        ///	获取当前系统根目录地址
        ///	</summary>
        ///	<returns type="string">返回系统根目录</returns>

        if (this.isNull(root) == false)
        {
            return root;
        }

        var fullPath = window.document.location.href;
        var strPath = window.document.location.pathname;
        var pos = fullPath.indexOf(strPath);
        var prePath = fullPath.substring(0, pos);
        var postPath = strPath.substring(0, strPath.substr(1).indexOf('/') + 1);

        if (siteType == 0)
        {
            root = prePath + postPath;
        }
        else
        {
            root = prePath;
        }

        return root;
    },

    //判断文本是否为空
    //奇怪,判断0也是为true
    isNull: function (str)
    {
        ///	<summary>
        ///		判断文本是否为空
        ///	</summary>
        ///	<param name="str" type="String">
        ///		请求判断的值
        ///	</param>
        ///	<returns type="bool">返回成功表示值为空，反之为失败</returns>

        if (str == null || str == undefined || str == "undefined" || str == "")
        {
            return true;
        }
        else
        {
            return false;
        }
    },

    //获取列表列值字符串，已逗号（,）进行分隔
    getListColumnStr: function (list, columnName)
    {
        var retStr = "";

        if(yds.isNull(list) == false )
        {
            var iCount = list.length;
            for (var i = 0; i < iCount; i++)
            {
                //如果无响应值,则默认不添加
                if (yds.isNull(list[i][columnName]) == false)
                {
                    retStr += "," + list[i][columnName];
                }
            }

            if (yds.isNull(retStr) == false)
            {
                retStr = retStr.substring(1, retStr.length);
            }
        }

        return retStr;
    },

    addTempUrl: function (theUrl, args)
    {
        ///	<summary>
        ///	为请求的URL添加临时变量,强制更新页面缓存
        ///	</summary>
        ///	<param name="theUrl" type="String">
        ///	请求添加的URL
        ///	</param>
        ///	<param name="args" type="String">
        ///	其他需要添加的参数
        ///	</param>
        ///	<returns type="bool">返回带临时变量的URL</returns>

        var argsUrl = (this.isNull(args) == false ? (args + "&") : "") + "temp=" + new Date().toString();
        var lastChar = theUrl.substring(theUrl.length - 1);
        switch (lastChar)
        {
            case "&":
            case "?":
                theUrl += argsUrl;
                break;
            default:
                if (theUrl.indexOf("?") != -1)
                {
                    theUrl += "&" + argsUrl;
                }
                else
                {
                    theUrl += "?" + argsUrl;
                }
                break;
        }

        return theUrl;
    },
    getJsonSerialize: function (objArgs)
    {
        ///	<summary>
        ///	根据对象名/对象等获取序列化的值(json)
        ///	</summary>
        ///	<param name="objArgs" type="Object">
        ///	对象名:可能是json/页面中的对象名/页面中的对象
        ///	</param>
        ///	<returns type="json">返回Json的序列化数据,可以向服务端提交</returns>

        var jsonArgs = null;
        if (this.isNull(objArgs)) return null;
        //判断是数组,直接返回
        if ($.isArray(objArgs))
        {
            return objArgs;
        }

        //判断
        if (typeof objArgs == "string")
        {
            var index = objArgs.indexOf("=");
            //如果是文本，则可能是URL串或指定的控件名称
            if (index == -1 && objArgs.substring(0, 1) != "{")
            {
                //如果不是URL串，那就认为是容器对象
                jsonArgs = $("#" + objArgs).find("*").serialize();
                if (this.isNull(jsonArgs))
                {
                    jsonArgs = $("#" + objArgs).serialize();
                }
            }
        }
        else
        {
            //isPlainObject:检测对象是否由{}或new object()的形式建立
            //如果是jquery对象或人工建立的array,json,均是
            //如果是页面的输入框等对象,则不是
            if ($.isPlainObject(objArgs) && this.isNull(objArgs.tagName))
            {
                //如果是纯粹的对象(由{}或new建立)
                //所有数组等以及jquery对象均是这种
                if (this.isNull(objArgs.jquery) == true)
                {
                    //如果查不到jquery版本,则此对象并非jquery对象,应该是数组/json等
                    jsonArgs = objArgs;
                }
                else
                {
                    //如果有jquery版本,则应该是传递进来的类似于$("#form")的对象
                    //区分form对象与其他对象
                    if (objArgs[0] == "[object HTMLFormElement]")
                    {
                        //对象为form,整体序列化即可
                        jsonArgs = objArgs.serialize();
                    }
                    else
                    {
                        //对象非form
                        jsonArgs = objArgs.find("*").serialize();
                        if (this.isNull(jsonArgs)) jsonArgs = objArgs.serialize();
                    }
                }
            }
            else
            {
                //剩下的为普通页面对象
                //但对于form对象与其他对象而言,form天然自带所有下级对象,可以直接序列化,其他对象不行
                if (objArgs.tagName == "FORM")
                {
                    jsonArgs = $(objArgs).serialize();
                }
                else
                {
                    jsonArgs = $(objArgs).find("*").serialize();
                    if (this.isNull(jsonArgs)) jsonArgs = $(objArgs).serialize();
                }
            }
        }

        return jsonArgs;
    },

    xmlPost: function (webFileUrl, objArgs, callBackFunc)
    {
        ///	<summary>
        ///		以同步(或异步)的方式无刷新求取数据&lt;br&gt;
        ///		其中参数objArgs，可有如下几种形式
        ///     1.URL形式，如xxx=xx&amp;xxx=xx&amp;xxx=..，此种形式将会被拼接至url中。
        ///     2.JSNO形式，如{'xx':'xxx','xxx':'x',...}，此种形式将会被POST至服务端。
        ///     3.String形式，控件容器对象名称，比如：form1,将自动将该名称对应的对象内部的所有内容POST至服务端。
        ///     4.Object形式，document.all('Form1')或$('#Form1')，将自动将该对象内部的所有内容POST至服务端。
        ///	</summary>
        ///	<param name="webFileUrl" type="String">
        ///		请求的页面地址,可带参数
        ///	</param>
        ///	<param name="objArgs" type="String">
        ///		传递的参数，一般使用JSON数据，也可直接使用页面对象请求序列化该对象所有信息
        ///	</param>
        ///	<param name="callBackFunc" type="Function">
        ///		表示采用异步形式取值，为空则同步方式，可空,写时可直接写：function(){语法}
        ///	</param>
        ///	<param name="mask" type="Boolean|loading">
        ///		自行定义的遮罩对象，可传递自定义的遮罩对象ID，或是否要求启用遮罩，或是传递名称请求加载loading遮罩;默认不产生遮罩
        ///	</param>
        ///	<returns type="String">返回请求的值</returns>	

        //定义要传递的JSON参数对象
        var jsonArgs = this.getJsonSerialize(objArgs);

        if (this.isNull(objArgs) == false)
        {
            //判断
            if (typeof objArgs == "string")
            {
                var index = objArgs.indexOf("=");
                //如果是文本，则可能是URL串或指定的控件名称
                if (index != -1)
                {
                    //有带＝号或&号的认为是URL串
                    webFileUrl = this.addTempUrl(webFileUrl, objArgs);
                }
            }
        }

        //临时存储数据
        var tmpData;
        //是否同步
        var async;

        //如果有主动传递callBack函数,则认为是异步方式
        //否则为同步请求
        if (callBackFunc)
        {
            async = true;
        }
        else
        {
            //为同步请求建立处理的函数
            async = false;
            callBackFunc = function (data) { tmpData = data; };
        }

        try
        {
            var x = $.ajax({
                url: webFileUrl,
                data: jsonArgs,
                type: 'Post',
                dataType: "json",
                async: async,
                cache: false,
                success: function (xa, xb, xc)
                {
                    var tmp = xa;
                    //先过滤一次
                    if (yds.isNull(xa) == false)
                    {
                        var result = xa;

                        //如果是Result格式;检测其state是否为-1/以及大于400
                        if (result.state == -1 || result.state > 400)
                        {
                            //如为以上情况,则表示出现错误了,将其置为空
                            //具体错误,由后续的complete事件统一处理
                            tmp = null;

                            //直接截断,不再进行后续的处理
                            return;
                        }
                        else
                        {
                            //呼叫请求的来源处
                            callBackFunc(tmp, xb, xc);
                        }
                    }
                    else
                    {
                        //呼叫请求的来源处
                        callBackFunc(tmp, xb, xc);
                    }
                },
                error: function (xa, xb, xc)
                {
                    alert("AJAX请求失败1");
                }
            });
        }
        catch (e)
        {
            tmpData = "脚本发生错误:\r\n" + e.message;
            if (async)
            {
                alert(tmpData);
            }
        }
        finally
        {
            if (async == false)
            {
                //针对同步方法,实际同步操作时不会弹出遮罩
                alert("访问服务器异常");
            }
        }

        return tmpData;
    },
    jsonpPost: function (webFileUrl, objArgs, callBackFunc)
    {
        ///	<summary>
        ///		异步的方式无刷新求取数据&lt;br&gt;
        ///	</summary>
        ///	<param name="webFileUrl" type="String">
        ///		请求的页面地址,可带参数
        ///	</param>
        ///	<param name="objArgs" type="String">
        ///		传递的参数，一般使用JSON数据，也可直接使用页面对象请求序列化该对象所有信息
        ///	</param>
        ///	<param name="callBackFunc" type="Function">
        ///		回调函数：function(){语法}
        ///	</param>

        //定义要传递的JSON参数对象
        webFileUrl = this.setData(webFileUrl,objArgs);
        $.getJSON(webFileUrl,
            function (ret)
            {
                callBackFunc(ret);
            });
    },
    setData: function (webFileUrl,data)
    {
        var str = '';
        for (var k in data)
        {
            if (data[k] == null) continue;
            str += '&' + k + '=' + data[k];
        }
        if (str != '') {
            str = str.substr(1);
            if (webFileUrl.indexOf('?') != -1) {
                webFileUrl += '&' + str;
            } else {
                webFileUrl += '?' + str;
            }

            webFileUrl += "&jsonpcallback=?";
        }
        else {
            webFileUrl += "?jsonpcallback=?";
        }

        return webFileUrl;
    },
    closeMask: function (mask)
    {
        ///	<summary>
        /// 统一关闭遮罩方法
        ///	</summary>
        ///	<param name="mask" type="Function|Boolean">
        ///	遮罩对象或是否请求遮罩（true/false）
        ///	</param>
        if (this.isNull(mask) == false)
        {
            //有请求产生遮罩时，要统一关闭
            if (mask == "mask")
            {
                mini.unmask();
            }
            else
            {
                mini.hideMessageBox(mask)
            }
            mask = null;
        }
    },

    callServer: function (actionUrl, dataArgs, callBackFunc, messageId)
    {
        ///	<summary>
        ///		客户端向服务器端请求执行某方法并且返回值
        ///	</summary>
        ///	<param name="actionUrl" type="String">
        ///		方法名,服务端action的全名,请在客户端以@Url.Content(~/xxx/xxx")的方式传递
        ///	</param>
        ///	<param name="dataArgs" type="json|objId">
        ///		参数集,可传递json对象,也可直接指定页面某对象ID,由脚本自行判断并取值
        ///	</param>
        ///	<param name="callBackFunc" type="Function">
        ///		需要采取异步方式时，自行构造的一个方法,可空
        ///     可直接写function(html){语法},其中html为返回的内容
        ///	</param>
        ///	<param name="messageId" type="string">
        ///		自行定义的遮罩对象ID,mini.loading/mini.tips等;默认为空
        ///	</param>
        ///	<returns type="object">根据服务端的返回值而定(一般返回json)</returns>
        try
        {
            return this.xmlPost(actionUrl, dataArgs, callBackFunc, messageId);
        }
        catch (e)
        {
            this.callError(e);
            return ("");
        }
    },
    getEditorValue: function (editorId)
    {
        ///<summary>
        /// 取得指定在线编辑器内容
        ///</summary>
        ///<param name="editorId">编辑器的名称(ID)</param>
        ///<returns type="String">返回在线编辑器的HTML内容</returns>

        var o_Text = "";
        try
        {
            var o_Editor = document.getElementById(editorId + "_Frame").contentWindow;
            o_Text = o_Editor.getText();
        }
        catch (e)
        {
            this.callError(e);
        }

        return o_Text;
    },

    //统一显示错误信息
    //注意:此函数仅应用于try{}catch(e){}中的统一显示,其他处错误显示应自行构造
    callError: function (e)
    {
        alert("脚本出现错误:\n" + typeof e == "String" ? e : e.message);
        if (mini) mini.unmask();
    },
    getStringLength: function (str)
    {
        ///<summary>
        /// 获取文本的字节长度
        ///</summary>
        ///<param name="str">需要计算的文本</param>
        ///<returns type="Number" />

        if (!str) { return 0; }
        //循环计数     
        var i = 0;
        var a = 0;
        for (i = 0; i < str.length; i++)
        {
            if (str.charCodeAt(i) > 255)
            {
                //按照预期计数增加2             
                a += 2;
            }
            else
            {
                a++;
            }
        }

        return a;
    },

    //截取字符串
    substr: function (str, len, isLeave)
    {
        ///<summary>
        /// 截取指定字节长度的字符串
        ///</summary>
        ///<param name="str">需要截取的文本</param>
        ///<param name="len">需要截取的长度</param>
        ///<param name="isLeave">是否在文本后添加省略号...,默认为是</param>
        ///<returns type="String" />

        if (!str || !len) { return ''; }
        if (this.isNull(isLeave)) isLeave = true;

        //预期计数：中文2字节，英文1字节     
        var a = 0;
        //循环计数     
        var i = 0;
        var k = 0;

        for (i = 0; i < str.length; i++)
        {
            str.charCodeAt(i) > 255 ? (a += 2) : (a++);

            k++;
            //如果增加计数后长度大于限定长度，就直接返回临时字符串         
            if (a >= len)
            {
                if (a > len) k--;
                break;
            }
        }

        //将当前内容加到临时字符串         
        var temp = str.substring(0, k) + (isLeave ? "..." : "");
        //alert(temp);
        //如果全部是单字节字符，就直接返回源字符串     
        return temp;
    },

    getEditorContent: function (contentId, typeName)
    {
        ///<summary>
        /// 返回在线编辑器内容
        ///</summary>
        ///<param name="contentId">该文本编辑器对应的存储控件的ID</param>
        ///<param name="typeName">希望请求的内容类型,html/text;默认为html</param>

        var editor = document.getElementById("eweb_" + contentId).contentWindow;
        var text = document.getElementById(contentId);
        var value = "";
        if (this.isNull(typeName)) typeName = "html";

        switch (typeName)
        {
            case "html":
                value = editor.getHTML();
                break;
            case "text":
                value = editor.getText();
                break;
            default:
                value = editor.getHTML();
                break;
        }

        //value = encodeURIComponent(value);

        return value;
    },
    getFormData: function (frmId, isData, isValid)
    {
        ///<summary>
        /// 返回Form内所有miniui控件的数据;默认带验证;如返回null表示验证不通过
        ///</summary>
        ///<param name="frmId" type="String">Form对象的ID</param>
        ///<param name="isData" type="Boolean">是否仅返回JSON对象,默认是;选否返回JSON文本</param>
        ///<param name="isValid" type="Boolean">是否同时验证.默认是</param>

        if (this.isNull(frmId)) frmId = "#form1";
        if (frmId.indexOf("#") != 0) frmId = "#" + frmId;

        var frm = new mini.Form(frmId);

        if (isValid != false)
        {
            frm.validate();
            if (frm.isValid() == false) return null;
        }

        var data = frm.getData(true, false);

        return isData != false ? data : mini.encode(data);
    },
    getCookie: function (name)
    {
        ///<summary>
        /// 获取cookies数据(文本值)
        ///</summary>
        ///<param name="name" type="String">cookie名称</param>
        ///<returns>返回文本的值</returns>
        var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
        if (arr != null) return unescape(arr[2]); return null;
    },
    setCookie: function (name, value, second)
    {
        ///<summary>
        /// 写入Cookie
        ///</summary>
        ///<param name="name" type="String">cookie名称</param>
        ///<param name="value" type="Object">值,必须为文本值,如是对象,应序列化为文本</param>
        ///<param name="second" type="Date">过期秒数(以当前时间为准)</param>
        var exp = new Date();
        exp.setTime(exp.getTime() + second * 1000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    },
    addBrowserFavorite: function (weburl, title)
    {
        ///<summary>
        /// 将指定地址添加到收藏夹
        ///</summary>
        ///<param name="weburl" type="String">要添加的地址</param>
        //if (jQuery.browser.msie)
        //{
        //    window.external.addFavorite(weburl, document.title);
        //}
        //else if (jQuery.browser.mozilla)
        //{
        //    window.sidebar.addPanel(document.title, weburl, "");
        //}
        //else
        //{
        //    alert("该浏览器不支持此操作，请手动设置");
        //}
        //return false;

        if (this.isNull(weburl))
        {
            weburl = window.location.href;
        }
        if (this.isNull(title))
        {
            title = document.title;
        }

        if (document.all)
        {
            window.external.addFavorite(weburl, title);
        }
        else if (window.sidebar)
        {
            window.sidebar.addPanel(title, weburl, "");
        }
        else
        {
            alert("该浏览器不支持此操作，请手动设置");
        }

        return false;
    },


    setHomepage: function (weburl)
    {
        ///<summary>
        /// 将指定地址设置为当前主页
        ///</summary>
        ///<param name="weburl" type="String">要设置的地址</param>
        if (jQuery.browser.msie)
        {
            document.body.style.behavior = 'url(#default#homepage)';
            document.body.setHomePage(weburl);
        }
        else
        {
            alert("该浏览器不支持此操作，请手动设置");
        }
        return false;
    },
    getLast: function (str, splitChar)
    {
        ///<summary>
        /// 从一段文本中根据指定的拆分字符拆为数组后取最后一个数组值
        ///</summary>
        ///<param type="String" name="str">需要拆分为数组的文本</param>
        ///<param type="String" name="splitChar">分割用的字符,默认为,号</param>
        ///<return>返回最后一个数组值</return>
        if (this.isNull(str)) return "";

        var ary = str.split(splitChar);
        var length = ary.length;
        return ary[length - 1];
    },
    getFirst: function (str, splitChar)
    {
        ///<summary>
        /// 从一段文本中根据指定的拆分字符拆为数组后取第一个数组值
        ///</summary>
        ///<param type="String" name="str">需要拆分为数组的文本</param>
        ///<param type="String" name="splitChar">分割用的字符,默认为,号</param>
        ///<return>返回第一个数组值</return>
        if (this.isNull(str)) return "";

        var ary = str.split(splitChar);
        var length = ary.length;
        return ary[0];
    },
    parseDate: function (dateString)
    {
        ///<summary>
        /// 将文本转换为日期对象,实际调用mini.parseDate(String)
        ///<para>支持的文本格式如下:</para>
        ///<para>2010-11-22</para>
        ///<para>2010/11/22</para>
        ///<para>11-22-2010</para>
        ///<para>11/22/2010</para>
        ///<para>2010-11-22 23:23:59</para>
        ///<para>2010/11/22 23:23:59</para>
        ///<para>2010-11-22T23:23:59</para>
        ///<para>2010/11/22T23:23:59</para>
        ///</summary>
        ///<param type="String" name="dateString">文本型的日期数据</param>
        ///<return>返回日期对象</return>

        return mini.parseDate(dateString);
    },
    formatDate: function (date, format)
    {
        ///<summary>
        /// 将日期对象转换为指定的格式文本,实际调用mini.formatDate(date,format)
        ///<para>默认格式化为yyyy-MM-dd</para>
        ///</summary>
        ///<param type="Date" name="date">日期对象</param>
        ///<param type="String" name="format">格式化形式,默认为yyyy-MM-dd ; 格式方式基本与C#日期格式方式相同</param>
        ///<return>返回文本型日期</return>
        if (this.isNull(format)) format = "yyyy-MM-dd";

        return mini.formatDate(date, format);
    },
    closeWindow: function (result)
    {
        ///<summary>
        /// 关闭miniui的弹出窗口
        ///</summary>
        ///<param name="result" type="Object">关闭窗口时需要返回的参数,一般调用yds.result()对象</param>
        if (window.CloseOwnerWindow)
        {
            return window.CloseOwnerWindow(result);
        }
        else
        {
            window.close();
        }
    },
    goBack: function ()
    {
        ///<summary>
        /// 网页脚本后退方法
        ///</summary>
        window.history.go(-1);
    },
    alert: function (message, callback)
    {
        ///<summary>
        /// 弹出警告框
        ///</summary>
        ///<param type="String" name="message">警告的消息内容</param>
        ///<param type="function" name="okCallBack">当确定之后需要执行的回调函数</param>
        ///<return>返回当前提示框的ID</return>
        var msg = "<div style='line-height:25px;text-align:left;font-weight:bold;padding-left:6px;'>" + message.split("\r\n").join("<br>") + "</div>";
        var msgid = mini.alert(msg, "警告", callback);
        return msgid;
    },
    confirm: function (message, okCallBack, cancelCallBack)
    {
        ///<summary>
        /// 弹出消息选择框
        ///</summary>
        ///<param type="String" name="message">弹出询问的消息内容</param>
        ///<param type="function" name="okCallBack">当确定之后需要执行的回调函数</param>
        ///<param type="function" name="cancelCallBack">当取消之后需要执行的脚本名称,一般为空,不执行</param>
        ///<return>返回当前提示框的ID</return>

        if (!okCallBack) { alert("错误:缺乏confirm执行体"); return; }
        //暂时如此处理
        message = "<div style='line-height:25px;text-align:left;font-weight:bold;'>" + message.split("\r\n").join("<br>") + "</div>";//"<div style='text-align:left'>" + message + "</div>";
        var msgid = mini.confirm(message, "消息窗",
            function (action)
            {
                if (action == "ok" && okCallBack)
                {
                    okCallBack();
                }
                else
                {
                    if (cancelCallBack)
                    {
                        cancelCallBack();
                    }
                    else
                    {
                        return;
                    }
                }
            }
        );
        return msgid;
    },
    loading: function (message)
    {
        ///<summary>
        /// 加载/等待提示框,返回对象ID；可使用yds.hideMessageBox关闭本对象
        ///</summary>
        ///<param type="String" name="message">等待消息;默认为:请稍等,数据正在操作中...</param>
        ///<return>返回当前提示框的ID</return>
        if (this.isNull(message)) message = "请稍等,数据正在加载中...";

        var msgid = mini.loading(message, "请稍等...");
        return msgid;
    },
    hideMessageBox: function (msgId)
    {
        ///<summary>
        /// 关闭弹出的提示框对象
        ///</summary>
        ///<param type="String" name="msgId">提示对象的ID</param>
        mini.hideMessageBox(msgId);
    },
    showTopTab: function (id, title, url, onlyUrl)
    {
        ///<summary>
        /// 在主框架上添加或更新标签；专用方法
        ///</summary>
        ///<param type="Object" name="tabs">标签对象</param>
        ///<param type="String" name="id">新标签的ID</param>
        ///<param type="String" name="title">标签名称</param>
        ///<param type="String" name="url">标签地址</param>
        ///<param type="Boolean" name="onlyUrl">是否只判断地址不判断参数；默认为否，需要一并判断参数</param>
        ///<return>如以标签形式打开则返回false；如以新页面形式打开，返回true</return>

        //默认主框架上有tabs名称为mainTabs
        var tabName = "mainTabs";
        var tabs = top.mini.get(tabName);

        if (this.isNull(tabs))
        {
            //如果找不到，则返回true
            return true;
        }
        else
        {
            //找到则调用showTab
            this.showTab(tabs, id, title, url, onlyUrl);
            return false;
        }
    },
    showTab: function (tabs, id, title, url, onlyUrl)
    {
        ///<summary>
        /// 添加或更新标签
        ///</summary>
        ///<param type="Object" name="tabs">标签对象</param>
        ///<param type="String" name="id">新标签的ID</param>
        ///<param type="String" name="title">标签名称</param>
        ///<param type="String" name="url">标签地址</param>
        ///<param type="Boolean" name="onlyUrl">是否只判断地址不判断参数；默认为否，需要一并判断参数</param>
        if (this.isNull(tabs))
        {
            alert("未传递有效的标签对象，无法创建标签！");
            return;
        }

        //统一为小写
        var tab = this.isNull(id) ? tabs.getTab(0) : tabs.getTab(id);
        if (!tab)
        {
            if (this.isNull(id) == false)
            {
                //如无对象,则创建
                tab = {};
                tab.name = id;
                tab.title = title;
                tab.showCloseButton = true;
                tab.url = url;

                tabs.addTab(tab);
            }
        }
        else
        {
            //如果二者的url地址不同,要求刷新.
            var frm = tabs.getTabIFrameEl(tab);
            if (frm == null) return;

            var isReload = false;
            var frmUrl = frm.contentWindow.location.href;
            var sourceUrl = onlyUrl ? frmUrl.split('?')[0] : frmUrl;
            toUrl = onlyUrl ? url.split('?')[0] : url;

            if (this.endWith(sourceUrl, toUrl) == false)
            {
                isReload = true;
            }

            if (isReload) tabs.loadTab(toUrl, tab)
        }

        tabs.activeTab(tab);
    },
    endWith: function (source, end)
    {
        ///<summary>
        /// 判断某文本是否以指定的文本结尾
        ///</summary>
        ///<param type="String" name="source">要判断的文本</param>
        ///<param type="String" name="end">指定的结尾文本</param>
        ///<returns>返回true/false</returns>
        var allLength = source.length;
        var index = source.indexOf(end);
        if (index == -1)
        {
            return false;
        }
        else
        {
            var checkLength = index + end.length;
            return allLength == checkLength;
        }
    },
    tips: function (content, state, timeout, callback)
    {
        ///<summary>
        /// 提示信息,默认中间弹出
        ///</summary>
        ///<param type="String" name="content">等待消息;默认为:请稍等,数据正在操作中...</param>
        ///<param type="String" name="state">消息框的颜色状态(或心情),有default|success|info|warning|danger,默认为default</param>
        ///<param type="Integer" name="timeout">显示时长,毫秒,默认1000毫秒(1秒)</param>
        ///<param type="Function" name="callback">回调事件,当指定的时长结束后执行</param>
        ///<return>返回当前提示框的ID</return>
        if (this.isNull(content)) content = "操作已经成功执行...";
        if (this.isNull(state)) state == "success";
        if (this.isNull(timeout)) timeout = 2000;

        var html = "<div style='font-weight:bold;margin-bottom:5px;'>提示信息</div><div>" + content + "</div>";
        var msgid = mini.showTips({ content: html, state: state, x: "center", y: "center", timeout: timeout });
        if (callback)
        {
            setTimeout(function () { callback(); }, timeout);
        }

        return msgid;
    },
    waiting: function (message, timeout)
    {
        ///<summary>
        /// 自行关闭的有标题的加载提示框;默认2.5秒后自动关闭
        ///</summary>
        ///<param type="String" name="message">等待消息;默认为:请稍等,数据正在加载中...</param>
        ///<param type="Number" name="timeout">自动关闭时间,单位:毫秒; 默认为2.5秒;</param>

        if (this.isNull(message)) message = "请稍等,数据正在加载中...";
        var msg = mini.loading(message, "Loading");

        setTimeout(function ()
        {
            mini.hideMessageBox(msg);
        }, timeout ? timeout : 2500);
        return msg;
    },
    prompt: function (message, callback)
    {
        ///<summary>
        /// (单行)文本输入提示框,可在callback中返回用户输入的信息,例:function(action,value){if(action=="ok"){...}}
        ///</summary>
        ///<param type="String" name="message">显示在单行输入框上方的提示性文字</param>
        ///<param type="String" name="callback">回调方法,可在callback中返回用户输入的信息,例:function(action,value){if(action=="ok"){...}}</param>
        var title = "请输入";
        var msg = mini.prompt(message, title, callback, false);
        return msg;
    },
    promptMulti: function (message, callback)
    {
        ///<summary>
        /// (多行)文本输入提示框,可在callback中返回用户输入的信息,例:function(action,value){if(action=="ok"){...}}
        ///</summary>
        ///<param type="String" name="message">显示在多行输入框上方的提示性文字</param>
        ///<param type="String" name="callback">回调方法,可在callback中返回用户输入的信息,例:function(action,value){if(action=="ok"){...}}</param>
        var title = "请输入";
        var msg = mini.prompt(message, title, callback, true);
        return msg;
    },
    notify: function (message, width, height, timeout, x, y)
    {
        ///<summary>
        /// 消息提示框(自动关闭),默认展示在屏幕右下角
        ///</summary>
        ///<param type="String" name="message">提示框显示文字,可使用HTML</param>
        ///<param type="Number" name="width">宽度,默认为280</param>
        ///<param type="Number" name="height">高度,默认为180</param>
        ///<param type="Number" name="timeout">自动关闭时间,毫秒;默认为3000毫秒</param>
        ///<param type="Number" name="x">x轴弹出方向,默认为right; 有left/center/right三种选项</param>
        ///<param type="Number" name="y">y轴弹出方向,默认为bottom,有top/middle/bottom三种选项</param>
        timeout = timeout ? timeout : 3000;
        var msg = mini.showMessageBox(
            {
                showModal: false,
                width: width ? width : 280,
                height: 180,
                title: "消息提示 - 将在" + (timeout / 1000) + "秒后自动关闭",
                iconCls: "mini-messagebox-warning",
                message: message,
                timeout: timeout ? timeout : 3000,
                x: x ? x : "right",
                y: y ? y : "bottom"
            });
        return msg;
    },
    showMessage: function (title, html, callback)
    {
        ///<summary>
        /// 弹出带有3个选择按钮(确定/否取消)的消息窗口
        ///</summary>
        ///<param type="String" name="title">提示框标题信息,默认为'信息提示'</param>
        ///<param type="String" name="html">显示在窗口中的HTML内容; 也支持直接传递带#号的对象ID进行显示,示例:#contentId</param>
        ///<param type="Function" name="callback">回调事件,传递参数action值为:ok/no/cancel;示例:function(action){if(action=='ok'){}}</param>
        if (this.isNull(title)) title = "信息提示";

        if (typeof html == "object")
        {
            //传递了完整对象进来
            //html = $(html).clone();
            //html.show();
            html = $(html).html();
        }
        else
        {
            if (html.substr(0, 1) == "#")
            {
                //表示直接取页面中指定对象的内容
                html = $(html).html(true);
            }
        }

        var options = {
            title: title,
            buttons: ["ok", "no", "cancel"],
            iconCls: "mini-messagebox-question",
            html: html,
            callback: callback
        };

        var msg = mini.showMessageBox(options);
        return msg;
    },
    options: function ()
    {
        ///<summary>
        /// miniui弹出窗口选项
        /// <para>url: 弹出窗口指向的地址</para>
        /// <para>title: 弹出窗口标题,默认为 '窗口操作'</para>
        /// <para>width: 宽度,默认为 640</para>
        /// <para>height: 高度,默认为 480</para>
        /// <para>iconCls: 在title之前的窗口图标,默认为icon-collapse</para>
        /// <para>resize: 允许尺寸调节,默认 允许</para>
        /// <para>drag: 允许拖拽位置,默认 允许</para>
        /// <para>close: 显示关闭按钮,默认 显示</para>
        /// <para>max: 显示最大化按钮,默认 显示</para>
        /// <para>modal: 显示遮罩,默认 显示</para>
        /// <para>onload: 当指向的页面加载完毕时的事件, 默认 null;代码格式:function(){} , 无参数</para>
        /// <para>onclose: 当弹出窗口关闭时的事件 , 默认 null;代码格式:function(result){....},result是返回值,自定义对象,可用来返回复杂的值;yds.result</para>
        ///</summary>
        var op = {};
        op.url = "";
        op.title = "窗口操作";
        op.width = 640;
        op.height = 480;
        op.iconCls = "icon-collapse";

        op.resize = true;
        op.drag = true;
        op.close = true;
        op.max = true;
        op.modal = true;

        op.onload = null;
        //var iframe = this.getIFrameEl();
        //iframe.contentWindow.SetData(null);

        op.onclose = null;

        return op;
    },
    getOpenWin: function (open)
    {
        ///<summary>
        /// 返回弹出窗口指向页面的window对象
        /// <para>open: 弹出窗口对象,指this; 如:onload : function(){ var win = yds.getOpenWin(this);win.mini.get("xxx").xxx }</para>
        ///</summary>
        var iframe = open.getIFrameEl();
        var win = iframe.contentWindow;
        return win;
    },
    result: function (state, value, message)
    {
        ///<summary>
        /// 通用的返回值对象;在弹出窗口中可在onclose()事件中直接调用closeWindow(result)返回对象
        /// <para>state: 返回对象状态,默认为0</para>
        /// <para>value: 返回值(当成功时),可自行定义任何对象或直接文本</para>
        /// <para>message: 需要返回的消息文本</para>
        ///</summary>
        var ret = {};
        ret.state = yds.isNull(state) ? "0" : state.toString();
        ret.value = yds.isNull(value) ? null : value;
        ret.message = yds.isNull(message) ? "" : message;
        return ret;
    },
    getOpenOwner:function()
    {
        ///<summary>
        /// 获取当前弹出窗口的父窗口(this.window.Owner);非parent或top
        ///</summary>
        return this.window.Owner;
    },
    open: function (url, options, title, width, height)
    {
        ///<summary>
        /// 返回弹出窗口指向页面的window对象
        ///</summary>
        ///<param type="String" name="url">弹出窗口地址</param>
        ///<param type="yds.options" name="options">自定义的弹出窗口参数对象;示例: new yds.options();如传递null内部将自动建立该对象</param>
        ///<param type="String" name="title">窗口标题 , 未传递默认为 '窗口操作'</param>
        ///<param type="number" name="width">窗口宽度,未传递默认为640</param>
        ///<param type="number" name="height">窗口高度,未传递默认为480</param>

        if (options == null) options = new this.options();

        options.url = url;
        if (this.isNull(title) == false) options.title = title;
        if (this.isNull(width) == false)
        {
            options.width = width;
        }
        if (this.isNull(height) == false) options.height = height;

        var open = mini.open({
            url: options.url,
            title: options.title,
            width: options.width,
            height: options.height,
            iconCls: options.iconCls,
            ondestroy: options.onclose,
            onload: options.onload,
            allowResize: options.resize,
            allowDrag: options.drag,
            showCloseButton: options.close,
            showMaxButton: options.max,
            showModal: options.modal
        });

        return open;
    },
    getEnumsById: function (enumList, enumKey)
    {
        ///<summary>
        /// 用于从自动生成的enums.js中根据枚举返回相应的枚举信息;如果找不到则返回null
        ///</summary>
        ///<param type="Object" name="enumList">枚举对象;该对象可从enums.js中根据枚举类名称获取,如:enums.StateTypes</param>
        ///<param type="Number" name="enumKey">枚举的值,一般是数字;</param>
        if (yds.isNull(enumKey) && enumKey != 0) enumKey = -1;

        for (var i = 0; i < enumList.length; i++)
        {
            var en = enumList[i];
            if (en.id == enumKey)
            {
                return en
            }
        }

        return null;
    },
    getEnumsByName: function (enumList, enumName)
    {
        ///<summary>
        /// 用于从自动生成的enums.js中根据枚举返回相应的枚举信息;如果找不到则返回null
        ///</summary>
        ///<param type="Object" name="enumList">枚举对象;该对象可从enums.js中根据枚举类名称获取,如:enums.StateTypes</param>
        ///<param type="String" name="enumName">枚举的名称</param>
        if (yds.isNull(enumKey)) enumKey = -1;

        for (var i = 0; i < enumList.length; i++)
        {
            var en = enumList[i];
            if (en.name == enumKey)
            {
                return en
            }
        }

        return null;
    },
    getGridError: function (grid)
    {
        ///<summary>
        /// 返回编辑表格中,验证不通过时的第一错误行第一错误列的错误信息
        ///</summary>
        ///<param type="Object" name="grid">表格对象</param>
        ///<returns>返回表格第一个错误行第一个错误列的错误信息</returns>
        var error = grid.getCellErrors()[0];//获取错误对象
        grid.beginEditCell(error.record, error.column);//找到第一行的第一个错误列,启动编辑
        return error.errorText;
    }
}

//所有AJAX请求完成后,都会触发本方法
$(document).ajaxComplete(function (evt, req, settings)
{
    //{"state":496,"value":"","message":"抛出未知错误"}
    var text = req.responseText;
    //检测是否为result对象
    if (text.indexOf("state") == 2)
    {
        var result = $.parseJSON(text);
        if (yds.isNull(result) == false)
        {
            dealAjaxResult(result);
        }
    }
});


function getUnknowError(message)
{
    ///	<summary>
    ///	用于错误处理,将错误消息进行包装,避免弹出窗口无法全部展示
    ///	</summary>
    var msg = "<div style='overflow-x:scroll;overflow-y:scroll;width:480px;height:220px;line-height:20px;text-align:left;'>" + message + "</div>";
    return msg;
}

function dealAjaxResult(ret)
{
    ///	<summary>
    ///	处理非正确数据,判断依据:result.state状态值为-1或大于400
    ///	</summary>

    //如果是Result格式;检测其state是否为-1/以及大于400
    if (ret != null && ret.state && (ret.state == -1 || ret.state > 400))
    {
        //是则直接拦截
        switch (ret.state)
        {
            case -1:
                //未知错误
                yds.alert(getUnknowError(yds.isNull(ret.message) ? "未知错误" : ret.message));
                break;
            case 496:
                //系统发生了未知错误,原地不动
                statusCode496(ret);
                break;
            case 497:
                //数据请求不合法,原地不动
                statusCode497(ret);
                break;
            case 498:
                //缺乏有效权限,跳转到首页
                statusCode498(ret);
                break;
            case 499:
                //尚未登录或登录失效,跳转到首页
                statusCode499(ret);
                break;
            default:
                //yds.alert(result.message);
                yds.alert(getUnknowError(yds.isNull(ret.message) ? "系统发生未定义的错误,无法操作." : ret.message));
                break;
        }
    }
}


$.ajaxSetup(
    {
        statusCode:
        {
            400: statusCode400,
            401: statusCode401,
            403: statusCode403,
            4039.9: statusCode4039,
            404: statusCode404,
            406: statusCode406,
            408: statusCode408,
            413: statusCode413,
            414: statusCode414,
            500: statusCode500,
            503: statusCode503,
            505: statusCode505
        }
    });

/*以下为自定义错误*/
//499 尚未登录或登录失效,弹出登录窗口，登录成功之后，用户可继续原有操作
function statusCode499(ret)
{
    var msg = getUnknowError(ret.message);
    //yds.alert("错误:<br>" + ret.message, function ()
    yds.alert(msg, function ()
    {
        if (yds.isNull(ret.value) == false)
        {
            //window.location.href = ret.value;
            var ops = new yds.options();
            ops.width = "480";
            ops.height = "320";
            var url = ret.value + "?isOpenLogin=true";
            yds.open(url, ops, "用户登录");
        }
    });
}
//498 缺乏有效权限,跳转到首页
function statusCode498(ret) { yds.alert(getUnknowError(ret.message), function () { if (yds.isNull(ret.value) == false) window.location.href = ret.value; }); }
//497 数据请求不合法,原地不动
function statusCode497(ret) { yds.alert(getUnknowError(ret.message)); }
//496 系统发生了未知错误,原地不动
function statusCode496(ret) { yds.alert(getUnknowError(ret.message)); }
/*以下为系统预定义错误*/
function statusCode400(data) { yds.alert("错误:<br>服务器不理解请求的语法。"); }
function statusCode401(data) { yds.alert("错误:<br>请求要求身份验证。对于登录后请求的网页，服务器可能返回此响应。"); }
function statusCode403(data) { yds.alert("错误:<br>服务器拒绝请求。"); }
function statusCode4039(data) { yds.alert("错误:<br>禁止访问：所连接的用户太多"); }
function statusCode404(data) { yds.alert("错误:<br>服务器找不到您所请求的文件或脚本。请检查URL以确保路径正确"); }
function statusCode406(data) { yds.alert("错误:<br>无法使用请求的内容特性响应请求的网页。"); }
function statusCode408(data) { yds.alert("错误:<br>服务器等候请求时发生超时。"); }
function statusCode413(data) { yds.alert("错误:<br>服务器无法处理请求，因为请求实体过大，超出服务器的处理能力。"); }
function statusCode414(data) { yds.alert("错误:<br>请求的 URI（通常为网址）过长，服务器无法处理。"); }
function statusCode500(data) { yds.alert("错误:<br>服务器遇到错误，无法完成请求。"); }
function statusCode503(data) { yds.alert("错误:<br>服务器目前无法使用（由于超载或停机维护）。通常，这只是暂时状态。"); }
function statusCode505(data) { yds.alert("错误:<br>服务器不支持请求中所用的 HTTP 协议版本。"); }