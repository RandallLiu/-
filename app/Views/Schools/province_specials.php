<div class="div-special-score mb-15">
    <select class="select input-180 select-sm" name="special-score-year" data-auth="login" onchange="filter_special_score_data('year')"></select>
    <select class="select input-180 select-sm" name="special-score-province" data-auth="login" onchange="filter_special_score_data('pro')"></select>
</div>

<div class="panel panel-flat">
    <table class="table datatable-selection-single table-hover province_special_score_list">
        <thead>
        <tr>
            <th width="60">序号</th>
            <th width="145px">专业名称</th>
            <th width="80px">类别</th>
            <th width="110px">批次名称</th>
            <th width="110px">类型</th>
            <th width="80px">最低分</th>
            <th width="120px" class="text-right">位次</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    $('.div-special-score select').select2({minimumResultsForSearch:-1})
    var province_special_score_data = [] , year_special_data = [];
    $(function (){ load_special_score_data(); })

    function load_special_score_data() {
        comm.doRequest('/v2/colleges/province_special_score',{id:'<?=$data['detail']['id']?>'},(resp)=>{
            if ( resp.data.length ) {
                var temp_year = [], temp_pro = [], temp_data;
                province_special_score_data = resp.data
                resp.data.map((item) => {temp_year.push(item.year);temp_pro.push(item.pro_code) });
                var year = [...new Set(temp_year)],province = [...new Set(temp_pro)];
                year.map((y) => {
                    var temp_pro = [];
                    province_special_score_data.map((item)=>{
                        if (y == item.year) temp_pro.push(item.pro_code)
                    })
                    const p = [... new Set(temp_pro)]
                    if (y) year_special_data[y] = p;
                })
                // year_special_data.map((k,v)=>{console.log(k,v)})
                set_year_options($('select[name=special-score-year]'),year)
                set_province_options($('select[name=special-score-province]'),year[0],year_special_data)
            }
            // filter_special_score_data()
        },'json');
    }
    //
    function render_province_special_score(data){
        var tb = "" ;
        // 循环
        $.each(data,function (k,v) {
            tb += ` <tr><td>${(k+1)}</td><td>${v.name.match(/^[^（]+/)}</td><td>${v.type}</td><td>${v.batch}</td><td>${v.zs}</td><td>${v.min}</td><td class="text-right">${v.section}</td></tr>`;
        });

        if ( tb=='' ) {
            tb += ` <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="text-right">-</td></tr>`;
        }
        $('.province_special_score_list tbody').html(tb);
    }
    //
    function filter_special_score_data(v) {
        var year = $('select[name=special-score-year]').val(),temp_data;
        if (v == 'year') set_province_options($('select[name=special-score-province]'),year,year_special_data);
        var province = $('select[name=special-score-province]').val()
        temp_data = province_special_score_data.filter((item)=>item.pro_code == province && item.year == year )
        render_province_special_score(temp_data);
    }
</script>