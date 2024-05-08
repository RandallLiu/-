<?php
    $cards = $data['cards']; $current_year  = $data['years'][0]; $sp_info = ""; $kemu = $data['kemu'];
?>
<?php foreach ( $cards as $k=>$card) :?>
    <tr class="main-tr main-<?=$card['school_id']?>">
        <td> <h6>志愿 <?=($k+1)?> </h6></td>
        <td> <h4><?=$card['name']?></h4>
            <span class="text-muted font13">
                院校代码: <?=substr($card['code_enroll'],0,5)?>
                <?=$card['type_name']?> <?=$card['nature_name']?>  <?=$card['admissions']?>
                <?=$card['f211']?> <?=$card['f985']?> <?=$card['doublehigh']?> <?=$card['dual_class_name']?>
            </span>
        </td>
        <td class="text-center">
            <h4><?=$card['odds']??0?> <span class="text-muted" style="font-size: 12px"> %</span></h4>
            <?php
            $cls = $card['odds'] <= 45 ? 'danger' : (($card['odds'] > 45 && $card['odds'] < 75) ? 'blue':'success');

            if ($card['odds'] <= 45 && $card['odds'] >= 0) {
                $text = '冲';
            }
            if ($card['odds'] > 45 && $card['odds'] <= 75) {
                $text = '稳';
            }
            if ($card['odds'] > 75) {
                $text = '保';
            }
            ?>
            <label class="label bg-<?=$cls?>-300"><?=$text?></label>
        </td>
        <td class="text-center">
            <div style="font-size: 20px;font-weight: bold">
                <?php foreach ($card['his_province_province_plans'] as $hc):?>
                    <?php if( $hc['year'] == $current_year ):?>
                        <?=$hc["nums"]?>
                    <?php endif;?>
                <?php endforeach;?>
                <span class="text-muted" style="font-size: 11px">人</span>
            </div>

            <span class="text-muted" style="font-size: 11px">
                                        <?php foreach ($card["his_province_scores"] as $hs):?>
                                            <?php if ( $current_year == $hs["year"] ):?>
                                                <?php $sp_info = $hs['sg_info'] ;?>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                        <?=$sp_info?>
                                    </span>
        </td>


        <td class="text-center" style="padding: 0px;border-bottom:1px solid #e2e2e2;" >
            <p style="border-bottom: 1px solid #e2e2e2;">最低分数</p>
            <p style="border-bottom: 1px solid #e2e2e2;">最低位次</p>
            <p style="margin-bottom: 0px;">招生人数</p>
        </td>

        <?php foreach ($data['years'] as $year):?>
            <?php $min = '-'; $section = '-'; $nums = '-';?>
            <?php foreach ($card["his_province_scores"] as $score):?>
                <?php if ( $year == $score["year"] ):?>
                    <?php $min = $score['min'] ; $section = $score['min_section'];?>
                <?php endif;?>
            <?php endforeach;?>

            <?php foreach ($card["his_province_province_plans"] as $plan):?>
                <?php if ( $year == $plan["year"] ):?>
                    <?php $nums = $plan['nums']; ?>
                <?php endif;?>
            <?php endforeach;?>

            <td width="10%" class="text-center" style="padding: 0px; border-bottom:1px solid #e2e2e2;">
                <p style="border-bottom: 1px solid #e2e2e2;"><?=$min?></p>
                <p style="border-bottom: 1px solid #e2e2e2;"><?=$section?></p>
                <p style="margin-bottom: 0px;"><?=$nums?></p>
            </td>
        <?php endforeach;?>

        <td class="text-right">
            <a class="label bg-danger-300 mb-10" href="/v2/aspiration/delete_card?id=<?=$card['school_id']?>" onclick="return comm.confirmCTL(this.href,'确认移除志愿卡?',(resp)=>{remove_card(true,<?=$card['school_id']?>)})"> <i class="icon  icon-bin" style="font-size: 11px"></i> 删除</a>
            <a class="label bg-success-300" data-state="expand" onclick="expand_collapse(this,'<?=$card['school_id']?>')"> <span class="a"> 折叠专业 </span> <span>( <?=count($card['children'])?> )</span><i class="icon icon-arrow-down22"></i></a>
        </td>
    </tr>

    <!--专业计划-->
    <?php if( $card['children'] ):?>
        <?php foreach ( $card['children'] as $key=>$child):?>
            <tr class="<?=$card["school_id"]?> child<?=$child['id']?>">
                <td style="text-indent: 12px;" class="text-center"> <h6><?=($k+1)?>-<?=($key+1)?></h6></td>
                <td style="text-indent: 12px;">
                    <h6><?=$child['spname']?></h6>
                    <span class="text-muted ml-10">
                        专业代码:<?=$child['spcode']?>  <?=$child['tuition']?"学费:{$child['tuition']}/年 ":''?>
                        <?=is_numeric($child['limit_year'])?" 学年: {$child['limit_year']} 年": " 学年:{$child['limit_year']}"?>
                    </span>
                </td>
                
                <td class="text-center">
                    <h6><?=$child['odds']??0?> <span class="text-muted" style="font-size: 12px"> %</span></h6>
                    <?php
                    $cls = $child['odds'] <= 35 ? 'danger' : (($child['odds'] > 35 && $child['odds'] < 75) ? 'blue':'success');
                    $text = "";

                    if ($child['odds'] < 10) {
                        $text = '风险大';
                    }
                    ?>
                    <label class="label bg-<?=$cls?>-300"><?=$text?></label>
                </td>

                <td class="text-center">
                    <div style="font-size: 20px;font-weight: bold">
                        <?php foreach ($child['plans'] as $hc):?>
                            <?php if( $hc['year'] == $current_year ):?>
                                <?=$hc["num"]?>
                            <?php endif;?>
                        <?php endforeach;?>
                        <span class="text-muted" style="font-size: 11px">人</span>
                    </div>
                    <span class="text-muted"><?=$child['sp_info']?></span>
                </td>

                <td class="text-center" style="padding: 0px;">
                    <p style="border-bottom: 1px solid #e2e2e2;">最低分数</p>
                    <p style="border-bottom: 1px solid #e2e2e2;">最低位次</p>
                    <p style="margin-bottom: 0px;">招生人数</p>
                </td>


                <?php foreach ($data['years'] as $year):?>
                    <?php $min = '-'; $section = '-'; $nums = '-';?>
                    <?php foreach ($child['scores'] as $score):?>
                        <?php if ( $score['special_id'] == $child['special_id'] && $score['year'] == $year):?>
                            <?php $min = $score['min'] ; $section = $score['min_section']; ?>
                        <?php endif;?>
                    <?php endforeach;?>

                    <?php foreach ($child['plans'] as $pa):
                        $pa = (array) $pa;
                        ?>
                        <?php if ( $pa['special_id'] == $child['special_id'] && $pa['year'] == $year):?>
                        <?php $nums = $pa['num'] ?>
                    <?php endif;?>
                    <?php endforeach;?>
                    <?php //$nums = $plan['num'] ?>

                    <td width="10%" class="text-center" style="padding: 0px;">
                        <p style="border-bottom: 1px solid #e2e2e2;"><?=$min?></p>
                        <p style="border-bottom: 1px solid #e2e2e2;"><?=$section?></p>
                        <p style="margin-bottom: 0px;"><?=$nums?></p>
                    </td>
                <?php endforeach;?>

                <td  class="text-right">
                    <a class="label bg-danger-300 mb-10" href="/v2/aspiration/delete?id=<?=$child['id']?>" onclick="return comm.confirmCTL(this.href,'确认移除志愿?',(resp)=>{remove_card(false,<?=$child['id']?>)})"> <i class="icon icon-bin" style="font-size: 11px"></i> 删除</a>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endif;?>

<?php endforeach;?>