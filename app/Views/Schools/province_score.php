<div class="div-pro-score mb-15">
    <select class="select input-180 select-sm" name="pro-score-year" data-auth="login" onchange="filter_province_score_data('year')"></select>
    <select class="select input-180 select-sm" name="pro-score-province" data-auth="login" onchange="filter_province_score_data('pro')"></select>
</div>

<div class="panel panel-flat">
    <table class="table datatable-selection-single table-hover data_province_score_list">
        <thead>
        <tr>
            <th width="60px">序号</th>
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
    $('.div-pro-score select').select2({minimumResultsForSearch:-1})
    var province_score_data = [] , year_province_data = [];
    $(function (){ load_pro_score_data(); })

    function load_pro_score_data() {
        comm.doRequest('/v2/colleges/province_score',{id:'<?=$data['detail']['id']?>'},(resp)=>{
            if ( resp.data.length ) {
                var temp_year = [], temp_pro = [], temp_data;
                province_score_data = resp.data
                resp.data.map((item) => {temp_year.push(item.year);temp_pro.push(item.pro_code) });
                var year = [...new Set(temp_year)],province = [...new Set(temp_pro)];
                year.map((y) => {
                    var temp_pro = [];
                    province_score_data.map((item)=>{
                        if (y == item.year) temp_pro.push(item.pro_code)
                    })
                    const p = [... new Set(temp_pro)]
                    if (y) year_province_data[y] = p;
                })
                // console.log("year_province_data:",year_province_data,year_province_data.length)
                // year_province_data.map((k,v)=>{console.log(k,v)})

                set_year_options($('select[name=pro-score-year]'),year)
                set_province_options($('select[name=pro-score-province]'),year[0],year_province_data)
            }
            // filter_province_score_data()
        },'json');
    }
    //
    function render_province_score(data){
        var tb = "" ;
        // 循环
        $.each(data,function (k,v) {
            tb += ` <tr><td>${k+1}</td><td>${v.type}</td><td>${v.batch}</td><td>${v.zs}</td><td>${v.min}</td><td class="text-right">${v.section}</td></tr>`;
        })
        if (tb=='') {
            tb += ` <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="text-right">-</td></tr>`;
        }
        $('.data_province_score_list tbody').html(tb);
    }

    //
    function filter_province_score_data(v) {
        var year = $('select[name=pro-score-year]').val(),temp_data;
        if (v == 'year') set_province_options($('select[name=pro-score-province]'),year,year_province_data);
        var province = $('select[name=pro-score-province]').val()
        temp_data = province_score_data.filter((item)=>item.pro_code == province && item.year == year )
        render_province_score(temp_data);
    }
</script>