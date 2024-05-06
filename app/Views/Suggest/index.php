<style>
    .frm_search .form-group{margin-bottom: 0px;}
    .pc {border-bottom: 1px solid #e2e2e2;padding: 5px 12px;margin-bottom: 0px;}
    code {font-size: 16px;}
</style>
<?php
    $kemu = session("kemu");
?>
<div class="content" >
    <div class="panel panel-flat">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_search">
                <select class="select selects-220" style="position: absolute;right: 15px" name="level">
                    <?php if( $kemu['batch'] != '10' ):?>
                        <option value="2001" selected>本科批</option>
                    <?php endif;?>
                    <option value="2002" <?=($kemu['batch'] == '10'?'selected':'')?>>专科批</option>
                </select>

                <input name="score_type" value="C" type="hidden">
                <input name="chose" value="schools" type="hidden">

                <div class="tabbable tab-content-bordered frma">

                    <ul class="nav nav-tabs nav-tabs-highlight" style="margin-bottom: 0px">
                        <li class="active"><a href="#schools" data-toggle="tab" data-status="schools" style="padding: 8px 12px"> 院校优先 </a></li>
                        <li class=""><a href="#special" data-toggle="tab" data-status="special"  style="padding: 8px 12px"> 专业优先 </a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane has-padding active" id="schools">
                            <div class="form-group">
                                <label class="col-md-1">院校所属 : </label>
                                <div class="col-md-11"> <?=\App\Libraries\LibComp::check('PROVINCE',['name'=>'province[]'])?> </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-lg-1">院校类型 : </label>
                                <div class="col-lg-11"> <?=\App\Libraries\LibComp::check('SCHTYPE',['name'=>'type[]'])?> </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-1">办学类型 : </label>
                                <div class="col-lg-11">
                                    <label class="mr-5"><input type="checkbox" value="36000" name="school_nature[]" > &nbsp;公办</label>
                                    <label class="mr-5"><input type="checkbox" value="36001" name="school_nature[]" > &nbsp;民办</label>
                                    <label class="mr-5"><input type="checkbox" value="36002" name="school_nature[]" > &nbsp;中外合作办学</label>
                                    <label class="mr-5"><input type="checkbox" value="36003" name="school_nature[]" > &nbsp;港澳台地区合作办学</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-1">院校特色 : </label>
                                <div class="col-lg-11">
                                    <label class="mr-5"><input type="checkbox" value="1" name="f985" > &nbsp;985</label>
                                    <label class="mr-5"><input type="checkbox" value="1" name="f211" > &nbsp;211</label>
                                    <label class="mr-5"><input type="checkbox" value="77004" name="doublehigh" > &nbsp;双高计划</label>
                                    <label class="mr-5"><input type="checkbox" value="双一流" name="dual_class_name" > &nbsp;双一流</label>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane has-padding" id="special">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <label class="col-md-1">专业门类 : </label>
                                <div class="col-md-10 level2"></div>
                            </div>

                            <div class="form-group div_level3_p1" style="display: none">
                                <label class="col-md-1">专业大类 : </label>
                                <div class="col-md-10 level3"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
        </div>

        <div class="panel-body" style="padding: 10px 20px">

            <a href="/mycard" class="btn bg-indigo-400" style="position: absolute; right: 20px"> 我的志愿卡 </a>
            <div class="tabbable frmb">
                <ul class="nav nav-pills nav-pills-toolbar text-left nav-xs" style="margin-bottom: 0px">
                    <li class=""><a href="#1" data-toggle="tab" data-status=""> 全部 </a></li>
                    <li class="active"><a href="#1" data-toggle="tab" data-status="C"> 冲击(<span class="cj">0</span>) </a></li>
                    <li class=""><a href="#1" data-toggle="tab" data-status="W"> 稳妥(<span class="wt">0</span>) </a></li>
                    <li class=""><a href="#1" data-toggle="tab" data-status="B"> 保底(<span class="bd">0</span>) </a></li>
                </ul>
            </div>
        </div>

        <table class="table table_special_list datatable-basic">
            <thead>
            <tr>
                <th width="225">学校</th>
                <th width="80" class="text-center">录取概率</th>
                <th width="90" class="text-center">历年</th>
                <?php foreach ($data['years'] as $year):?>
                <th width="90" class="text-center"><?=$year?></th>
                <?php endforeach;?>
                <th width="90" class="text-right">操作</th>
            </tr>
            </thead>
        </table>

    </div>
</div>

<script src="/resource/app/specials.js"></script>

<script>
    var tbspecial , year = <?=$data['years'][0]?> , has_kemu = <?=$kemu?"true":"false"?>;
    $(function(){
        $("select[name=level]").select2({minimumResultsForSearch:-1});
        tbspecial = comm.dt({
            ele : $('.table_special_list'),
            url : '/v2/colleges/suggest_test?' + $('.frm_search').serialize(),
            columns:['name','odds','id',null,null,null],
            columnDefs : [
                {
                    aTargets:[0],
                    mRender:function(data,full){
                        return `<span class="h5" style="min-width: 320px;">
                                <a href="/colleges/detail?id=${full.id}" class="hModal" data-size="lg" data-yes="N" lang="${data}">${comm.ellipsis(data,data,320)}</a></span>
                                <div>
                                    <span class="text-muted font12"> ${full.province_name} ${full.school_type} ${full.nature_name}  ${full.f211=='1'?'211':''}  ${full.f985=='1'?'985':''}  ${full.dual_class_name?full.dual_class_name:''}  ${full.admissions=='1'?'强基计划':''} ${full.doublehigh=='77004' ? '双高计划':''}</span>
                                </div>`;
                    },
                },
                {
                    aTargets:[1],
                    mRender:function(data,full){
                        return `<div class="text-center">
                                    <h4>${data}<span class="text-muted" style="font-size: 12px"> %</span></h4>
                                    <label class="label ${(full.t == 'C' || full.t == 'N') ? 'bg-danger':(full.t == 'W'?'bg-blue':'bg-success')}-300">${full.tn}</label>
                                </div>`;
                    },
                },

                {
                    aTargets:[2],
                    mRender:function(data,full){
                        return `<div class="text-center">
                            <p class="pc">最低分</p>
                            <p class="pc">位次</p>
                            <p style="margin-bottom: 0px;padding: 5px 12px;">招生人数</p></div>`;
                    },
                },

                {
                    aTargets:[3],
                    mRender:function(data,full){
                        var a = {scores:[{year:full.year,min:full.min_score,proscore:full.proscore,section:full.section}],plan:full.last_plan};
                        return render(a,year);
                    },
                },
                {
                    aTargets:[4],
                    mRender:function(data,full){
                        return render(data,year-1);
                    },
                },
                {
                    aTargets:[5],
                    mRender:function(data,full){

                        return render(data,year-2);
                    },
                },
                {
                    aTargets:[6],
                    mRender:function(data,full){
                        return `<div class="text-right"> <a class="btn btn-sm bg-success-300 hModal" lang="${full.name}" data-size="full" data-yes="N" href="/report?id=${full.id}&level=${$('select[name=level]').val()}&score_type=${$('input[name=score_type]').val()}&odds_id=${full.odds_id}&odds=${full.odds}"> 专业填报 </a> </div>`;
                    },
                },
            ],
            drawCallback:function ( setting ) {
                $.each(setting.json.tab,function (k,v){$(`.${k}`).text(v);});
                var rows = tbspecial.api().rows({ page: 'current' }).nodes();
                var style = {"padding":"0px","border-left":"1px solid #eee","border-right":"1px solid #eee"};
                $.each(rows,function (k,v) {for (var i = 1 ; i <= 5 ; i++) $(v).find(`td:eq(${i})`).css(style);});
            }
        });
        $('.frma>ul>li>a').on('click',function () {
            if (!has_kemu) {
                $('a.aprovince').click(); return false;
            }
            var status = $(this).data('status'); $('input[name=chose]').val(status);
            load_suggest_data();
        });
        $('.frmb>ul>li>a').on('click',function () {
            if (!has_kemu) {
                $('a.aprovince').click();return false;
            }
            var status = $(this).data('status'); $('input[name=score_type]').val(status);
            load_suggest_data();
        });
        $('#schools input[type=checkbox]').on('click',function () {
            if (!has_kemu) {
                $('a.aprovince').click();return false;
            }
            load_suggest_data();
        })
        load_level2_data();

        <?php if (!$kemu):?>
            $('a.aprovince').click();
        <?php endif;?>


    });

    // 输入
    function render(data,year){
        var down_score = '-', section = '-' , plan = '-' ,scores = data.scores ? data.scores : data, plans=data.plan;
        // 分数
        if ( scores ) scores.map((v)=>{ if( v.year==year ) {down_score = v.min;section = v.section} });
        // 招生计划
        if ( plans ) plans.map((v)=>{ if( v.year==year ) {plan = v.num} });

        return `<div class="text-center">
            <p class="pc">${down_score}</p>
            <p class="pc">${section}</p>
            <p style="margin-bottom: 0px;padding: 5px 12px;">${plan}</p></div>
        `;

        //return `<div style="width: 120px;"><table width="110px"><tr><td>${down_score}</td></tr><tr><td>${section}</td></tr><tr><td>${plan}</td></tr></table></div>`;
    }

    function load_suggest_data(){
        tbspecial.fnReloadAjax('/v2/colleges/suggest_test?' + $('.frm_search').serialize());
    }

    $('.frm_search select[name=level]').on('change',function () {
        load_level2_data()
    });

    // 加载 门类
    function load_level2_data(){
        var  level1 = $('.frm_search').find('select[name=level]').val();
        level1 = level1 == '2001' ? 1 : 2;
        const level2_data = specials.filter(item=>item.id == level1);
        var level2_html = "<label class='mr-5'> <input name='level2_id' value='' type='radio' checked onclick='load_level3_data(0)'> 全部 </label>";

        level2_data[0].child.map((item,k)=>{
            level2_html += `<label class="mr-5"> <input name="level2_id" value="${item.id}" type="radio" onclick="load_level3_data(${item.id})"> ${item.name} </label>`;
        });
        $('.level2').html(level2_html);
        load_level3_data('')
    }

    // 加载 大类
    function load_level3_data(level2){
        var level1 = $('.frm_search').find('select[name=level]').val(),level3_html = "";
        if (level1 && level2) {
            level1 = level1 == '2001' ? 1 : 2;
            const level2_data = specials.filter(item => item.id == level1);
            const level3_data = level2_data[0].child.filter(item => item.id == level2);

            if (level3_data.length) {
                level3_html = "<label class='mr-5'> <input name='level3_code' value='' type='radio' checked  onclick='load_suggest_data()'> 全部 </label>";
                level3_data[0].child.map((item,k)=>{
                    level3_html += `<label class="mr-5"> <input name="level3_code" value="${item.id}" type="radio" onclick="load_suggest_data()"> ${item.name} </label>`;
                });
                $('.div_level3_p1').css("display","");
            }
        } else {
            $('.div_level3_p1').css("display","none");
        }
        $('.level3').html(level3_html);
    }
</script>