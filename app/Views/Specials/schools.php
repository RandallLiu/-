
<!-- Content area -->
<div class="content pb-0" style="margin-bottom: 0px;padding-bottom: 0px;">
    <div class="panel panel-flat mb-0" style="margin-bottom: 0px;padding-bottom: 0px;">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_special_search">
                <input name="special_id" value="<?=$data['id']?>" type="hidden">
                <div class="form-group">
                    <label class="col-md-2">院校所属 : </label>
                    <div class="col-md-10"> <?=\App\Libraries\LibComp::check('PROVINCE',['name'=>'province_id[]'])?> </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2"></label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="has-feedback has-feedback-left">
                                <input type="text" class="form-control " name="keys" placeholder="查询 学校名称 , 编码 , 学校电话">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary search" onclick="load_special_school_data()">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table_special_school_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-center">序号</th>
                <th width="120">学校名称</th>
                <th width="80">学校编码</th>
                <th width="90">综合排名</th>
                <th width="100">性质</th>
                <th width="80">省份</th>
                <th width="80">院校类型</th>
                <th width="80">层次</th>
                <th width="60">985</th>
                <th width="60">211</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    var sctable;
    $(function(){
        var scroll_height = (document.body.scrollHeight/2)-132;
        sctable = comm.dt({
            ele : $('.table_special_school_list'),
            // sort:true,
            // order:[[4,'desc'],[2,'desc'],[3,'desc']],
            url : '/v2/colleges/special_schools?' + $('.frm_special_search').serialize(),
            scrollY : scroll_height,
            scrollCollapse:true,
            columns:['rownum','name','code','rank','school_type','province','type_name','level_name','is985','is211'],
            columnDefs : [
                {aTargets:[0],mRender:function(data,full){return `<div class="text-center">${data}</div>`;},orderable:false},
                {
                    aTargets:[1],
                    mRender:function(data,full){
                        return `<a href="/colleges/detail?id=${full.id}" class="hModal" data-size="lg" data-yes="N" data-group="special_school" lang="${data}">${comm.ellipsis(data,data,240)}</a>` ;
                    },
                },
                {
                    aTargets:[8,9],
                    mRender:function(data,full){
                        return data == "1" ? '是':'--';
                    },
                },
            ],

            drawCallback:function ( setting ) {
            }
        });
    });
    function load_special_school_data(){
        sctable.fnReloadAjax('/v2/colleges/special_schools?' + $('.frm_special_search').serialize());
    }
    $('.frm_special_search input[type=checkbox]').on('click',function () {
        load_special_school_data()
    });
    setTimeout(()=>{
        load_special_school_data()
    },500)
</script>
