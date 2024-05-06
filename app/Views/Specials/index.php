<style>
    .frm_search .form-group{margin-bottom: 0px;}
</style>
<!-- Content area -->
<div class="content" >
    <div class="panel panel-flat mb-0">
        <div class="panel-body">
            <form action="#" class="form-horizontal frm_search">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1">专业层次 : </label>
                    <div class="col-md-10">
                        <label class="mr-5"> <input type="radio" name="level1" value="1" checked> 本科</label>
                        <label> <input type="radio" name="level1" value="2"> 专科</label>
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
                                <input type="text" class="form-control " name="keys" placeholder="查询 专业名称 , 大类名称">
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

        <table class="table table_special_list datatable-basic">
            <thead>
            <tr>
                <th width="60" class="text-left ml-20">序号</th>
                <th>专业名称和代码</th>
                <th width="45">专业门类</th>
                <th width="120">专业大类</th>
                <th width="90" class="text-right">学年制</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script src="/resource/app/specials.js"></script>
<script>
    var tbspecial;
    $(function(){
        // var scroll_height = document.body.scrollHeight - 280;
        tbspecial = comm.dt({
            ele : $('.table_special_list'),
            url : '/v2/specials/data?' + $('.frm_search').serialize(),
            // scrollY : scroll_height,
            // scrollCollapse:true,
            columns:['rownum','name','level2_name','level3_name','limit_year'],
            columnDefs : [
                {
                    aTargets:[0],
                    mRender:function(data,full){
                        return `<div class="text-left ml-5">${data}</div>`;
                    },
                },
                {
                    aTargets:[1],
                    mRender:function(data,full){
                        return `<a href="/v2/specials/schools?id=${full.id}" class="hModal" data-size="lg" data-yes="N">${data} (${full.spcode})</a>`;
                    },
                },
                {
                    aTargets:[4],
                    mRender:function(data,full){
                        return `<div class="text-right">${data}</div>`;
                    },
                    // orderable:true
                }
            ]
        });

        load_level2_data()
    });


    function load_specail_data(){
        tbspecial.fnReloadAjax('/v2/specials/data?' + $('.frm_search').serialize());
    }

    $('.frm_search input[name=level1]').on('click',function () {
        load_level2_data()
    });


    // 加载 门类
    function load_level2_data(){
        const level1 = $('.frm_search').find('input[name=level1]:checked').val();
        const level2_data = specials.filter(item=>item.id == level1)
        var level2_html = "<label class='mr-5'> <input name='level2' value='' type='radio' checked onclick='load_level3_data(0)'> 全部 </label>";
        console.log(level1,level2_data)
        level2_data[0].child.map((item,k)=>{
            level2_html += `<label class="mr-5"> <input name="level2" value="${item.id}" type="radio" onclick="load_level3_data(${item.id})"> ${item.name} </label>`;
        });
        $('.level2').html(level2_html);
        load_level3_data('')
    }

    // 加载 大类
    function load_level3_data(level2){
        const level1 = $('.frm_search input[name=level1]:checked').val();//,level2 = $('.frm_search input[name=level2]').val();
        var level3_html = "<label class='mr-5'> <input name='level3' value='' type='radio' checked  onclick='load_specail_data()'> 全部 </label>";
        if (level1 && level2) {
            const level2_data = specials.filter(item => item.id == level1)
            const level3_data = level2_data[0].child.filter(item => item.id == level2)
            console.log(level3_data)
            if (level3_data.length) {
                level3_data[0].child.map((item,k)=>{
                    level3_html += `<label class="mr-5"> <input name="level3" value="${item.id}" type="radio" onclick="load_specail_data()"> ${item.name} </label>`;
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