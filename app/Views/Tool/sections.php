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

                <div class="form-group">
                    <label class="col-lg-1">考生类型 : </label>
                    <div class="col-lg-11 kslx">
                        <?php foreach ($data["kslx"] as $i=>$item):?>
                            <?php $k = ""; $v = "";?>
                            <?php foreach ($item as $key=>$value) :?>
                                <?php $k = $key; $v = $value?>
                            <?php endforeach;?>
                            <label class="mr-10"> <input type="radio" name="type" value="<?=$k?>" <?=($kemu['typeId'] == $k ? "checked" : ($i==0?'checked':''))?>> <?=$v?> </label>
                        <?php endforeach;?>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-left">序号</th>
                <th width="120">分数</th>
                <th width="80">本段人数</th>
                <th width="120" class="text-right">总计人数</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    var table , km;
    $(function(){
        load_json();
        table = comm.dt({
            ele : $('.table_list'),
            url : '/v2/tool/section_data?' + $('.frm_search').serialize(),
            columns:['rownum','score','num','total'],
            columnDefs : [
                {
                    aTargets:[0],
                    mRender:function(data,full){
                        return `<div class="left">${data}</div>`;
                    }
                },

                {
                    aTargets:[3],
                    mRender:function(data,full){
                        return `<div class="text-right">${data}</div>`;
                    }
                },
            ]
        });
    });

    function load_data(){
        table.fnReloadAjax('/v2/tool/section_data?' + $('.frm_search').serialize());
    }

    $('.frm_search input[type=radio]').on('click',function () {
        load_province_km();
        load_data();
    });

    function load_province_km() {
        const province = $('input[name=province_id]:checked').val() ;var radios = '' ,i = 0;
        var tekm = km[province][2023];
        $.each(tekm,function (k,v){
            if (k !== 'level') radios += `<label class="mr-10"> <input type="radio" name="type" value="${v.id}" ${i==0 ? 'checked' : ''} onclick="load_data()"> &nbsp;${v.name} </label>`;
            i++;
        });
        $('.kslx').html(radios);
    }

    function load_json(){
        fetch("/uploads/data/kemu.dict.json").then(response=>response.json()).then(data=>{km = data}).catch(error => {console.error("读取失败!",error)})
    }
</script>