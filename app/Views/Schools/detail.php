<?php
    $model = $data['detail']; $kemu = session('kemu');
?>
<script>
    // 设置
    function set_year_options(_this,data){
        var options = "";
        $.each(data , function (k,v) {options+=`<option value="${v}">${v}</option>`});
        _this.html(options).val(data[0]).trigger('change');
    }

    // 设置省份选项
    function set_province_options(_this,yearValue,data) {
        var options = "",province = '<?=$kemu?$kemu['province']:''?>' , sel = false,
            defaultValue = (province&&sel)?province:data[yearValue][0];

        $.each(data[yearValue] , function (k,v) {
            if (v == province) sel = true;options+=`<option value="${v}">${province_data[v]}</option>`
        });
        _this.html(options).val(defaultValue).trigger('change');
    }
</script>
<h6>基本信息</h6>
<table class="table table-bordered mb-20">
    <tr>
        <td width="15%">学校名称</td>
        <td colspan="5"><?=$model['name']?>
            <label class="label bg-primary-300"><?=$model['level_name']?></label>
            <label class="label bg-primary-300"><?=$model['type_name']?></label>
            <label class="label bg-primary-300"><?=$model['school_nature_name']?></label>
            <?php if( $model['f985']=='1' ):?>
                <label class="label bg-primary-300">985</label>
            <?php endif;?>

            <?php if( $model['f211']=='1' ):?>
                <label class="label bg-primary-300">211</label>
            <?php endif;?>

            <?php if( $model['dual_class_name'] ):?>
                <label class="label bg-primary-300"><?=$model['dual_class_name']?></label>
            <?php endif;?>

            <?php if( $model['admissions'] == '1' ):?>
                <label class="label bg-primary-300">强基计划</label>
            <?php endif;?>
        </td>
    </tr>
    <tr>
        <td width="15%">省/市/地址</td>
        <td colspan="5"><?=$model['province_name'] . $model['city_name'] . $model['address'] ?></td>

    </tr>

    <tr>
        <td width="15%">官方网址</td>
        <td colspan="5"><?=$model['school_site']?> <?=$model['site']?></td>
    </tr>

    <tr>
        <td width="15%">官方电话</td>
        <td colspan="5"><?=$model['phone']?></td>
    </tr>
    <?php if ( $model['num_doctor'] || $model['num_master'] || $model['num_subject'] ):?>
    <tr>
        <td  colspan="6">

            <?php if( $model['num_doctor'] ):?>
            <span class="mr30">
                博士点: <label class="label bg-info-300"> <?=$model['num_doctor']?> </label>
            </span>
            <?php endif;?>

            <?php if( $model['num_master'] ):?>
                <span class="mr30">
                硕士点: <label class="label bg-info-300"> <?=$model['num_master']?> </label>
                </span>
            <?php endif;?>

            <?php if( $model['num_subject'] ):?>
                <span class="mr-30">
                科研项目: <label class="label bg-info-300"> <?=$model['num_subject']?> </label>
                </span>
            <?php endif;?>
        </td>
    </tr>
    <?php endif;?>

    <tr>
        <td width="15%">简介</td>
        <td colspan="5"><?=$model['content']?></td>
    </tr>
</table>

<div class="tabbable">
    <ul class="nav nav-xs nav-tabs nav-tabs-solid nav-tabs-component nav-justified">
        <li class="active"><a href="#tab1" data-toggle="tab" aria-expanded="true">开设专业</a></li>
        <li class=""><a href="#tab2" data-toggle="tab" aria-expanded="false">省分线数</a></li>
        <li class=""><a href="#tab3" data-toggle="tab" aria-expanded="false">专业分数线</a></li>
        <li class=""><a href="#tab4" data-toggle="tab" aria-expanded="false">招生计划</a></li>
        <li class=""><a href="#tab5" data-toggle="tab" aria-expanded="false">录取预测</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <?=view('/Schools/dual_class',['data'=>$data])?>
        </div>

        <div class="tab-pane" id="tab2">
            <?=view('/Schools/province_score',['data'=>$data])?>
        </div>

        <div class="tab-pane" id="tab3">
            <?=view('/Schools/province_specials',['data'=>$data])?>
        </div>

        <div class="tab-pane" id="tab4">
            <?=view('/Schools/specials_plan',['data'=>$data])?>
        </div>

        <div class="tab-pane" id="tab5">
            录取预测
        </div>
    </div>
</div>
