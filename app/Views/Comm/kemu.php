<?php $kemu = $data['kemu']; $province = $data['province']; ?>
<div class="form-group">
    <?php if ($kemu['choose_type'] == 1):?>
        <input type="hidden" name="typeId" value="3">
    <?php endif;?>

    <label class="col-lg-2 control-label"><?=($kemu['type'] == '3'?'选择科目':'科目信息')?><span class="text-danger">*</span> : </label>
    <div class="col-lg-10 <?=(($kemu['type']=='1'||$kemu['type']=='2')?'again':'')?>">
    <?php foreach ( $kemu['course_item'] as $item ):?>
        <label class="<?=(($kemu['type']=='2'|| $kemu['type']=='1')?"checkbox-inline":'radio-inline')?>">
            <input
                    type="<?=(($kemu['type']=='2'|| $kemu['type']=='1')?'checkbox':'radio')?>"
                    name="<?=(($kemu['type']=='3'|| $kemu['type']=='0')?'typeId':'kemu[]')?>" class="kemu"
                    value="<?=(($kemu['type']=='3')?($item['subjectID']==2?'2073':'2074'):($item['subjectID'] == 0 ? '1' : ($item['subjectID'] == 1 ? '2' :$item['subjectID'])))?>"
            >
            <?=$item['subjectName']?>
        </label>
    <?php endforeach;?>
    </div>
</div>

<?php if($kemu['type'] == '3'):?>
<div class="form-group">
    <label class="col-lg-2 control-label">再选科目(选择两门) <span class="text-danger">*</span>: </label>
    <div class="col-lg-10 again">
        <?php foreach ( $kemu['course_item_second'] as $item ):?>
            <label class="checkbox-inline"><input type="checkbox" name="kemu[]" class="kemu" value="<?=$item['subjectID']?>"> <?=$item['subjectName']?> </label>
        <?php endforeach;?>
    </div>
</div>
<?php endif;?>

<script>
    const len = <?=($kemu['type']=='3'?2:3)?>;
    $(".again input").on("click",function () {
        var c = $(".again input:checked");
        $(".again input[type=checkbox]").each(function (k,item){
            if (!$(item).prop("checked") && c.length == len) $(item).attr("disabled","disabled"); else $(item).removeAttr("disabled");
        });
    });
</script>
