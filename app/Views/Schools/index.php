<style>
    .frm_search .form-group{margin-bottom: 0px;}
</style>
<!-- Content area -->
<div class="content">
    <div class="panel panel-flat">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_search">
                <div class="form-group">
                    <label class="col-md-1">院校所属 : </label>
                    <div class="col-md-11"> <?=\App\Libraries\LibComp::check('PROVINCE',['name'=>'province_id[]'])?> </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-1">院校类型 : </label>
                    <div class="col-lg-11"> <?=\App\Libraries\LibComp::check('SCHTYPE',['name'=>'type[]'])?> </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-1">办学类型 : </label>
                    <div class="col-lg-11">
                        <?=\App\Libraries\LibComp::check('LEVEL',['name'=>'level[]'])?>
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

                <div class="form-group">
                    <label class="col-lg-1"></label>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <div class="has-feedback has-feedback-left">
                                <input type="text" class="form-control " name="keys" placeholder="查询 学校名称 , 编码 , 学校电话">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary search" onclick="load_data()">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-center">序号</th>
<!--                <th width="45">ICON</th>-->
                <th width="120">学校名称</th>
                <th width="80">学校编码</th>
                <th width="90">综合排名</th>
                <th width="100">性质</th>
                <th width="80">省份</th>
                <th width="80">城市</th>
                <th width="80">上级部门</th>
                <th width="80">院校类型</th>
                <th width="80">层次</th>
                <th width="60">985</th>
                <th width="60">211</th>
                <th width="70">双一流</th>
                <th width="70">双基计划</th>
                <th width="70">双高计划</th>
                <th width="70"><span class="year"></span>招生总数</th>
                <th width="110"><span class="pro"></span>最低分/位次</th>
                <th width="70">一流学科数</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    var table;
    $(function(){
        var scroll_height = document.body.scrollHeight - 355;
        table = comm.dt({
            ele : $('.table_list'),
            sort:true,
            order:[[1,'desc'],[2,'desc'],[3,'desc']],
            url : '/v2/colleges/data?' + $('.frm_search').serialize(),
            // scrollY : scroll_height,
            // scrollCollapse:true,
            columns:['rownum','name','code','rank','school_type','province','city','belong','type_name','level_name','is985','is211','dual_name','qiangji','doublehigh','plan_nums','pro_min_score','dual_class_nums'],
            columnDefs : [
                {aTargets:[0],mRender:function(data,full){return `<div class="text-center">${data}</div>`;},orderable:false},
                /*
                {
                    aTargets:[1],
                    mRender:function(data,full){
                        return data  ? `<img src="${data}" width="20" height="20">`:'--';
                    },
                    orderable:false
                },*/
                {
                    aTargets:[1],
                    mRender:function(data,full){
                        return `<a href="/colleges/detail?id=${full.id}" class="hModal" data-size="lg" data-yes="N" lang="${data}">${comm.ellipsis(data,data,240)}</a>` ;
                    },
                    orderable:true
                },
                {
                    aTargets:[10,11],
                    mRender:function(data,full){
                        return data == "1" ? '是':'--';
                    },
                    orderable:true
                },

                {
                    aTargets:[5,6,7,4],
                    mRender:function(data,full){
                        return comm.ellipsis(data,data,80);
                    },
                    orderable:true
                },

                {
                    aTargets:[13],
                    mRender:function(data,full){
                        return data == "1" ? '强基计划':'--';
                    },
                    orderable:true
                },

                {
                    aTargets:[14],
                    mRender:function(data,full){
                        return data == "77004" ? '双高计划':'--';
                    },
                    orderable:true
                },

                {
                    aTargets:[16],
                    mRender:function(data,full){
                        const pid = comm.PROID();

                        if (data) {
                            return (data[pid] && data[pid].min) ? (data[pid].min + '/' + data[pid].section) : '--'
                            // return ''
                        }
                        return ""
                    },
                    orderable:false
                },

                {
                    aTargets:[17],
                    mRender:function(data,full){
                        return data;
                    },
                    orderable:false
                }
            ],

            drawCallback:function ( setting ) {
                $('.year').text(setting.json.year)
                $('.pro').text(setting.json.year + comm.PROVINCE(setting.json.province))
            }
        });
        // $('#DataTables_Table_0_wrapper').minHeight(scroll_height)
    });
    function load_data(){
        table.fnReloadAjax('/v2/colleges/data?' + $('.frm_search').serialize());
    }

    $('.frm_search input[type=checkbox],input[type=radio]').on('click',function () {
        load_data()
    });

    // 设置
    function set_year_options(_this,data){
        var options = "";
        $.each(data , function (k,v) {
            options+=`<option value="${v}">${v}</option>`
        })
        _this.html(options).val(data[0]).trigger('change');
    }

    //
    function set_province_options(_this,yearValue,data) {
        console.log(yearValue,data)
        var options = "";
        $.each(data[yearValue] , function (k,v) {
            options+=`<option value="${v}">${province_data[v]}</option>`
        })
        _this.html(options).val(data[yearValue][0]).trigger('change');
    }
</script>
