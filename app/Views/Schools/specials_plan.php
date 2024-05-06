<div class="div-plan-score mb-15">
    <select class="select input-180 select-sm" name="special-plan-year" data-auth="login" onchange="filter_special_plan_data('year')"></select>
    <select class="select input-180 select-sm" name="special-plan-province" data-auth="login" onchange="filter_special_plan_data('pro')"></select>
</div>

<div class="panel panel-flat">
    <table class="table datatable-selection-single table-hover special_plan_list">
        <thead>
        <tr>
            <th width="85">序号</th>
            <th width="160px">专业名称</th>
            <th width="80px">类别</th>
            <th width="120px">批次名称</th>
            <th width="110px">类型</th>
            <th width="110px">计划招生</th>
            <th width="100px">学费</th>
            <th width="100px" class="text-right">学制</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    $('.div-plan-score select').select2({minimumResultsForSearch:-1})
    var special_plan_data = [] , plan_special_data = [] , c = 0;
    $(function (){ load_special_plan_data(); })

    function load_special_plan_data() {
        comm.doRequest('/v2/colleges/province_plan_score',{id:'<?=$data['detail']['id']?>'},(resp)=>{
            if ( resp.data.length ) {
                var temp_year = [], temp_pro = [], temp_data;
                special_plan_data = resp.data
                resp.data.map((item) => {temp_year.push(item.year);temp_pro.push(item.pro_code) });
                var year = [...new Set(temp_year)],province = [...new Set(temp_pro)];
                year.map((y) => {
                    var temp_pro = [];
                    special_plan_data.map((item)=>{
                        if (y == item.year) temp_pro.push(item.pro_code)
                    })
                    const p = [... new Set(temp_pro)]
                    if (y) plan_special_data[y] = p;
                })
                // plan_special_data.map((k,v)=>{console.log(k,v)})

                set_year_options($('select[name=special-plan-year]'),year);
                set_province_options($('select[name=special-plan-province]'),year[0],plan_special_data);
            }
            // filter_special_plan_data()
        },'json');
    }
    //
    function render_special_plan_score(data){
        var tb = "" ;
        // 循环
        $.each(data,function (k,v) {
            tb += ` <tr><td>${(k+1)}</td><td>${v.name}</td><td>${v.type}</td><td>${v.batch}</td><td>${v.zs}</td><td>${v.num}</td><td>${v.tuition}</td><td class="text-right">${v.school_year}</td></tr>`;
        });

        if (tb=='')  tb += ` <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="text-right">-</td></tr>`;

        $('.special_plan_list tbody').html(tb);
    }

    //
    function filter_special_plan_data(v) {
        console.log("plan",v)
        var year = $('select[name=special-plan-year]').val(),temp_data;
        if ( v == 'year' ) set_province_options($('select[name=special-plan-province]'),year,plan_special_data);
        var province = $('select[name=special-plan-province]').val();
        temp_data = special_plan_data.filter((item)=>item.pro_code == province && item.year == year )
        render_special_plan_score(temp_data);
    }
</script>