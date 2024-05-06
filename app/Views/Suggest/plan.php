<?php
    $current_year  = $data['years'][0]; $sp_info = ""; $plans = $data['plans']; $kemu = session('kemu');
?>

<?php foreach ( $plans as $item ):
    $item = (array) $item;?>

    <?php if ($item['year'] == $current_year) :?>
        <tr>
            <td>
                <div style="text-indent: 10px;">
                    <?php preg_match("/^[^（]+/", $item['spname'], $matches);?>
                    <h6><?=$item['spname2']?:$matches[0]?></h6>
                    <span class="text-muted ml-10">
                        专业代码:<?=$item['spcode']?>  <?=$item['tuition']?"学费:{$item['tuition']}/年 ":''?>
                        <?=is_numeric($item['limit_year'])?" 学年: {$item['limit_year']} 年": " 学年:{$item['limit_year']}"?>
                    </span>
                </div>
            </td>
            <td class="text-center">
                <h6><?=$item['odds']?:'1'?> <span class="text-muted" style="font-size: 12px"> %</span> </h6>
                <?php
                    $cls = $item['odds'] <= 35 ? 'danger' : (($item['odds'] > 35 && $item['odds'] < 75) ? 'blue':'success');
                    $text = "风险大";

                if ($item['odds'] <= 35 && $item['odds'] > 10) {
                    $text = '冲';
                }
                if ($item['odds'] > 35 && $item['odds'] <= 75) {
                    $text = '稳';
                }
                if ($item['odds'] > 75) {
                    $text = '保';
                }
                ?>
                <label class="label bg-<?=$cls?>-300"><?=$text?></label>
            </td>
            <td class="text-center" style="padding: 5px">
                <div style="font-size: 20px;font-weight: bold">
                    <?=$item['num']?:'-'?> <span class="text-muted" style="font-size: 11px">人</span>
                </div>
                <span class="text-muted" style="font-size: 11px;">
                    <?php if (!$item["sp_info"]): ?>
                        <?php foreach ($data['scores'] as $score):?>
                            <?php if ( $score['special_id'] == $item['special_id'] && $score['year'] == $current_year):?>
                                <?php $sp_info = $score['sg_info']; ?>
                            <?php endif;?>
                        <?php endforeach; endif;?>
                    <?=$item["sp_info"]?:$sp_info?>
                </span>
            </td>

            <td class="text-center" style="padding: 0px;">
                <p style="border-bottom: 1px solid #e2e2e2;">最低分数</p>
                <p style="border-bottom: 1px solid #e2e2e2;">最低位次</p>
                <p style="border-bottom: 0px">招生人数</p>
            </td>

            <?php foreach ($data['years'] as $year):?>
                <?php $min = '-'; $section = '-'; $nums = '-';?>
                <?php foreach ($data['scores'] as $score):?>
                    <?php if ( $score['special_id'] == $item['special_id'] && $score['year'] == $year):?>
                        <?php $min = $score['min'] ; $section = $score['min_section']; ?>
                    <?php endif;?>
                <?php endforeach;?>

                <?php foreach ($data['plans'] as $pa):
                    $pa = (array) $pa;
                    ?>
                    <?php if ( $pa['special_id'] == $item['special_id'] && $pa['year'] == $year):?>
                        <?php $nums = $pa['num'] ?>
                    <?php endif;?>
                <?php endforeach;?>

                <td width="10%" class="text-center" style="padding: 0px;">
                    <p style="border-bottom: 1px solid #e2e2e2;"><?=$min?></p>
                    <p style="border-bottom: 1px solid #e2e2e2;"><?=$section?></p>
                    <p style="border-bottom: 0px"><?=$nums?></p>
                </td>

            <?php endforeach;?>
            <td  class="text-center">
                <?php if(!$item["has_plan"]):?>
                    <a href="#" class="btn btn-sm bg-orange-300" onclick="do_aspiration(<?=$item["id"]?>,'<?=$item["odds"]?>')"> 填报 </a>
                <?php else :?>
                    <a href="/v2/aspiration/delete?id=<?=$item['id']?>" class="btn btn-sm bg-danger-300" onclick="return comm.confirmCTL(this.href,'确认移除该志愿专业?',(resp)=>{load_plan_items();})"> 移除 </a>
                <?php endif;?>
            </td>
        </tr>
    <?php endif;?>
<?php endforeach;?>