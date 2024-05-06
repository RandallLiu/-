<style>
    a.current,button.current {border:1px solid #fd8858}
</style>
<form action="/v2/kemu/selected" class="form-horizontal frm_kemu_select" data-load="true">
    <div class="form-group">
        <label class="col-lg-2  control-label">高考地区 <span class="text-danger">*</span>: </label>
        <div class="col-lg-10"> <?=\App\Libraries\LibComp::select('PROVINCE',['name'=>'province','class' => 'select','req'],'',false)?> </div>
    </div>
    <div class="xuanke"></div>
    <div class="form-group">
        <label class="col-lg-2  control-label">预估分数 <span class="text-danger">*</span>: </label>
        <div class="col-lg-10">
            <input class="form-control" name="score" placeholder="请输入 预估分数" onkeypress="return comm.iNum()" max="950" min="100" required>
            <input type="hidden" name="batch" value="">
            <input type="hidden" name="section" value="0">
            <div class="rank"></div>
        </div>

    </div>
</form>
<script>
    $(".frm_kemu_select select").select2({minimumResultsForSearch:-1});
    var kemu;
    $(function (){
        $('.frm_kemu_select select[name=province]').on('click',function (){fn_kemu(); get_score()});

        $('.frm_kemu_select input[name=score]').on('change',function (){
            get_score();
        });

        <?php if(session('kemu')):?>
            var kemu = <?=json_encode(session('kemu'))?>;
            $('select[name=province]').val(kemu.province).trigger('change');fn_kemu();
            console.log(kemu)
            // 实例化 Promise
            const cb = new Promise((resolve,reject)=>{setTimeout(()=>{resolve()},200)});
            // settimeout
            cb.then(()=>{comm.formload($(".frm_kemu_select"),kemu);}).then(()=>{
                if ((kemu.typeId == '3' && kemu.kemu.length == 3) || ((kemu.typeId == '2073' || kemu.typeId == '2074') && kemu.kemu.length == 2)) {
                    $(".again input[type=checkbox]").map((k, v) => {
                        if (!v.checked) $(v).attr("disabled", "disabled");
                    });
                }
            });
        <?php else :?>
            fn_kemu();
        <?php endif;?>


    });

    function fn_kemu(){
        var province = $('.frm_kemu_select select[name=province]').val();
        comm.doRequest("/v2/kemu/get_kemu_item",{province:province},(resp)=>{$('.xuanke').html(resp)});
    }

    // 获取分数
    function get_score(){
        var province = $('.frm_kemu_select select[name=province]').val(),
            value = $('.frm_kemu_select input[name=score]').val() , type = $('.frm_kemu_select input[name=typeId]').val();
        if (value > 100 && type) comm.doRequest(`/v2/kemu/get_sections_score`, {province_id:province,score:value,type:type}, (resp) => {if (resp.data) $('.rank').html(`${resp.data.batch},分数位于${resp.data.total}`); $('input[name=batch]').val(resp.data?resp.data.batch_code:10); $('input[name=section]').val(resp.data.total)}, 'json')
    }

</script>