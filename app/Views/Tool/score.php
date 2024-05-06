<style>
    .frm_search .form-group{margin-bottom: 0px;}

</style>
<?php
    $kemu = session('kemu');
?>
<!-- Content area -->
<div class="content">
    <div class="panel panel-flat">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_search">

                <div class="form-group">
                    <label class="col-lg-1">所属区域 : </label>
                    <div class="col-lg-11"> <?=\App\Libraries\LibComp::radio('PROVINCE',['name'=>'province_id'],$kemu?$kemu['province']:11)?> </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-1">年份 : </label>
                    <div class="col-lg-11">
                        <?php foreach ($data["years"] as $k=>$year):?>
                            <label class="mr-10"> <input type="radio" name="year" value="<?=$year?>" <?=($year==$data['current_year']?'checked':'')?>> <?=$year?> </label>
                        <?php endforeach;?>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-center">序号</th>
                <th width="120">所属地区</th>
                <th width="80">年份</th>
                <th width="100">类别</th>
                <th width="220">批次</th>
                <th width="120">分数线</th>
                <th width="120" class="text-right">最低位次</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    var table;
    $(function(){
        table = comm.dt({
            ele : $('.table_list'),
            url : '/v2/tool/score_data?' + $('.frm_search').serialize(),
            // scrollY : scroll_height,
            // scrollCollapse:true,
            columns:['rownum','province','year','tname','batch','score','section'],
            columnDefs : [
                {aTargets:[0],mRender:function(data,full){return `<div class="text-center">${data}</div>`;},orderable:false},
                {aTargets: [6],
                    mRender:function (data,full){
                        return `<div class="text-right">${data}</div>`
                    }},
            ]
        });
    });

    function load_data(){
        table.fnReloadAjax('/v2/tool/score_data?' + $('.frm_search').serialize());
    }

    $('.frm_search input[type=checkbox],input[type=radio]').on('click',function () {
        load_data()
    });
</script>