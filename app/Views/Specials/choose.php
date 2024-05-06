<style>
    .frm_search .form-group{margin-bottom: 0px;}
</style>
<?php
    $kemu = session("kemu");
?>
<div class="content" >
    <div class="panel panel-flat">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_search">
                <input name="favorite" value="0" type="hidden">

                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1">专业层次 : </label>
                    <div class="col-md-10">
                        <label class="mr-5"> <input type="radio" name="special_type" value="1" checked> 本科 </label>
                        <label> <input type="radio" name="special_type" value="2"> 专科(高职) </label>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1">专业门类 : </label>
                    <div class="col-md-10 level2"></div>
                </div>

                <div class="form-group div_level3_p1" style="display: none">
                    <label class="col-md-1">专业大类 : </label>
                    <div class="col-md-10 level3"></div>
                </div>

                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1"></label>
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="has-feedback has-feedback-left">
                                <input type="text" class="form-control " name="keys" placeholder="查询 专业名称">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary search" onclick="load_specail_data()">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="panel-body" style="padding: 10px 20px">
            <div class="tabbable">
                <ul class="nav nav-pills nav-pills-toolbar text-left nav-xs" style="margin-bottom: 0px">
                    <li class="active"><a href="#0" data-toggle="tab" data-status="0">全部</a></li>
                    <li class=""><a href="#1" data-toggle="tab" data-status="1"> 我的收藏 ( <span class="fnum">0</span> )</a></li>
                </ul>
            </div>
        </div>

        <table class="table table_special_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-center ml-20">序号</th>
                <th>收藏</th>
                <th style="padding-left: 15px; width: 285px;">学校</th>
                <th width="145">专业</th>
                <th width="130">批次</th>
                <th width="90">学制</th>
                <th width="90">省份</th>
                <th width="90">城市</th>
                <th width="90">招生类型</th>
                <th width="90"><?=$data['year'].($kemu?(\App\Libraries\LibComm::$province[$kemu['province']]):'北京')?>年总招生</th>
                <th width="90"><?=$data['year']?>年最低分数线</th>
                <th width="90"><?=$data['year']?>年最低排位</th>
            </tr>
            </thead>
        </table>

    </div>
</div>
<script src="/resource/app/specials.js"></script>

<script>
    var tbspecial;
    $(function(){
        tbspecial = comm.dt({
            ele : $('.table_special_list'),
            url : '/v2/colleges/search_specials?' + $('.frm_search').serialize(),
            // scrollY : scroll_height,
            // scrollCollapse:true,
            columns:['rownum','sch_spe_id','name','special_name','batch_name','limit_year','province_name','city_name','type_name','nums','min','section'],
            columnDefs : [
                {
                    aTargets:[0],
                    mRender:function(data,full){
                        return `<div class="text-center ml-5">${data}</div>`;
                    },
                },

                {
                    aTargets:[1],
                    mRender:function(data,full){
                        var text = "+ 收藏",cls = "bg-success-300";
                        if (full.favorite) {
                            text = "- 取消";  cls = "bg-danger-300";
                        }
                        return `<a href="/v2/colleges/school_special_favorite?id=${data}" class="label ${cls}" onclick="return comm.doRequest(this.href,{},(resp)=>{if(resp.code) {load_specail_data();comm.Alert(resp.msg,true);} else comm.Alert(resp.msg,false)},'json')">${text}</a>`;
                    },
                },

                // {
                //     aTargets:[2],
                //     mRender:function(data,full){
                //         return comm.ellipsis(data,data,285);
                //     },
                // },
                {
                    aTargets:[3],
                    mRender:function(data,full){
                        return comm.ellipsis(data,data,125);
                    },
                },

            ],

            drawCallback:function ( setting ) {
                $(`.fnum`).text(setting.json.fnum);
                // 合并行
                var api = tbspecial.api();
                var rows = api.rows({ page: 'current' }).nodes();

                // 第一列合并
                var idx = 2;
                var last = null;
                var tr = null;
                var ltd = null;
                api.column(idx, { page: 'current' }).data().each(function (group, i) {
                    tr = $(rows[i]);
                    var td = $("td:eq(" + idx + ")", tr);
                    if (last !== group) {
                        td.attr("rowspan", 1);
                        td.html(comm.word_break( group,260));
                        ltd = td;
                        last = group;
                        td.css({"vertical-align":"middle","padding-left":"20px","font-weight":"blod"});
                    } else {
                        ltd.attr("rowspan", parseInt(ltd.attr("rowspan")) + 1);
                        td.remove();
                    }
                });
            },
        });
        load_level2_data();

        $('.tabbable>ul a').on('click',function () {
            var status = $(this).data('status'); $('input[name=favorite]').val(status);
            load_specail_data();
        });
    });

    function load_specail_data(){
        tbspecial.fnReloadAjax('/v2/colleges/search_specials?' + $('.frm_search').serialize());
    }

    $('.frm_search input[name=special_type]').on('click',function () {
        load_level2_data()
    });
    // 加载 门类
    function load_level2_data(){
        const level1 = $('.frm_search').find('input[name=special_type]:checked').val();
        const level2_data = specials.filter(item=>item.id == level1)
        var level2_html = "<label class='mr-5'> <input name='level2_id' value='' type='radio' checked onclick='load_level3_data(0)'> 全部 </label>";
        console.log(level1,level2_data)
        level2_data[0].child.map((item,k)=>{
            level2_html += `<label class="mr-5"> <input name="level2_id" value="${item.id}" type="radio" onclick="load_level3_data(${item.id})"> ${item.name} </label>`;
        });
        $('.level2').html(level2_html);
        load_level3_data('')
    }

    // 加载 大类
    function load_level3_data(level2){
        const level1 = $('.frm_search input[name=special_type]:checked').val();
        var level3_html = "<label class='mr-5'> <input name='level3_code' value='' type='radio' checked  onclick='load_specail_data()'> 全部 </label>";
        if (level1 && level2) {
            const level2_data = specials.filter(item => item.id == level1)
            const level3_data = level2_data[0].child.filter(item => item.id == level2)
            console.log(level3_data)
            if (level3_data.length) {
                level3_data[0].child.map((item,k)=>{
                    level3_html += `<label class="mr-5"> <input name="level3_code" value="${item.id}" type="radio" onclick="load_specail_data()"> ${item.name} </label>`;
                });
                $('.div_level3_p1').css("display","");
            }
        } else {
            $('.div_level3_p1').css("display","none");
        }
        $('.level3').html(level3_html);
        load_specail_data()
    }
</script>