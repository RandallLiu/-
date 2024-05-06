<?php $kemu = $data['kemu']; $province = $data['province']; ?>

<?php if (!$kemu):?>
    <div class="form-group">
        <label class="col-lg-3 control-label">科目信息 <span class="text-danger">*</span>: </label>
        <div class="col-lg-9 one">
            <label class="checkbox-inline"><input type="radio" name="typeId" class="kemu" value="1" onclick="get_score()" required> 理科 </label>
            <label class="checkbox-inline"><input type="radio" name="typeId" class="kemu" value="2" onclick="get_score()" required> 文科 </label>
        </div>
    </div>
<?php else :?>
    <div class="form-group">
        <label class="col-lg-2 control-label">选择科目<span class="text-danger">*</span> : </label>
        <div class="col-lg-10 one">
            <?php foreach ( $kemu as $k=>$v ):?>
                <?php foreach ( $v as $key=>$value):?>
                    <?php if($key != '3'):?>
                        <label class="radio-inline"><input type="radio" name="typeId" class="kemu" onclick="get_score()" value="<?=$key?>" required><?=$value?> </label>
                    <?php else:?>
                    <div class="again">
                        <input type="hidden" name="typeId" value="3">
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70000"> 物理 </label>
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70001"> 历史 </label>
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70002"> 化学 </label>
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70003"> 生物 </label>
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70004"> 政治 </label>
                        <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70005"> 地理 </label>
                    </div>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endforeach;?>
        </div>
    </div>

    <?php if(!array_key_exists('3',$kemu[0]) && !array_key_exists('1',$kemu[0])):?>
        <div class="form-group">
            <label class="col-lg-2 control-label">再选科目(选择两门) <span class="text-danger">*</span>: </label>
            <div class="col-lg-10 again">
                <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70002"> 化学 </label>
                <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70003"> 生物 </label>
                <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70004"> 政治 </label>
                <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="70005"> 地理 </label>
            </div>
        </div>
    <?php endif;?>
<?php endif;?>


<script>
    const len = <?=($kemu && array_key_exists('3',$kemu[0]))?3:2?>;
    $(".again input").on("click",function () {
        var c = $(".again input:checked");
        $(".again input[type=checkbox]").each(function (k,item){
            if (!$(item).prop("checked") && c.length == len) $(item).attr("disabled","disabled"); else $(item).removeAttr("disabled");
        });
    });
</script>