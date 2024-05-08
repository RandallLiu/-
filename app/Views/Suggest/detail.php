<?php
    $school =(array) $data['school']; $current_year  = $data['years'][0]; $sp_info = "";
    $argc = $data['argc'];
?>
<style>
    .report p{margin-bottom: 0px;padding: 5px 12px;}
    .report thead th{position: sticky;background-color: #f9f9f9;top: -21px; z-index: 999; border-top:1px solid #e2e2e2}
    .report thead td{position: sticky;background-color: #f9f9f9;top: 9px; z-index: 1000; border-top:1px solid #e2e2e2;}
</style>
<table class="table table-bordered report table-hover">
    <thead>
    <tr>
        <th width="25%">招生院校专业</th>
        <th width="10%" class="text-center">录取概率</th>
        <th width="10%" class="text-center"><?=$current_year?>招生计划</th>
        <th width="12%" class="text-center">历年</th>
        <?php foreach ($data['years'] as $year):?>
            <th width="11%" class="text-center"><?=$year?></th>
        <?php endforeach;?>
        <th width="10%" class="text-center">
            操作
        </th>
    </tr>
    <tr>
        <td style="border-bottom:1px solid #e2e2e2;">
            <h4><?=$school['name']?></h4>
            <span class="text-muted font13">
                    院校代码: <?=substr($school['code_enroll'],0,5)?>
                <?=$school['type_name']?> <?=$school['nature_name']?>  <?=$school['admissions']==1?"强基计划":''?>
                <?=$school['f211']=='1'?'211':''?> <?=$school['f985']=='1'?'985':''?>
                </span>
        </td>

        <td class="text-center" style="border-bottom:1px solid #e2e2e2;"><h4><?=$school['odds']??0?> <span class="text-muted" style="font-size: 12px"> %</span></h4></td>

        <td class="text-center" style="border-bottom:1px solid #e2e2e2;">
            <div style="font-size: 20px;font-weight: bold">
                <?php foreach ($school['total_plan_data'] as $it):?>
                    <?php if ( $it["year"] == $current_year ):?>
                        <?=$it["nums"]?>
                    <?php endif;?>
                <?php endforeach;?>
                <span class="text-muted" style="font-size: 11px">人</span>
            </div>

            <span class="text-muted" style="font-size: 11px">
                    <?php foreach ($school["total_score_data"] as $score):?>
                        <?php if ( $current_year == $score["year"] ):?>
                            <?php $sp_info = $score['sg_info'] ;?>
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
            <?php foreach ($school["total_score_data"] as $score):?>
                <?php if ( $year == $score["year"] ):?>
                    <?php $min = $score['min'] ; $section = $score['min_section'];?>
                <?php endif;?>
            <?php endforeach;?>

            <?php foreach ($school["total_plan_data"] as $plan):?>
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
        <td class="text-center" style="border-bottom:1px solid #e2e2e2;"></td>
    </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
    // 初始
    $(function () {load_plan_items()});

    // 加载招生计划
    function load_plan_items(){
        comm.doRequest("/v2/colleges/plan_item",{"id":'<?=$argc['id']?>',"level":"<?=$argc['level']?>","odds_id":"<?=$argc['odds_id']?>",'stype':"<?=$argc['score_type']?>"},(resp)=>{$("table.report>tbody").html(resp);});
    }

    // 保存填报志愿
    function do_aspiration(id,odds){
        comm.doRequest("/v2/aspiration/save",{"id":id,"batch":'<?=$argc['batch']?>','level':'<?=$argc['level']?>','score_type':'<?=$argc['score_type']?>','odds':odds,'odds_id':<?=$argc['odds_id']?>,'sch_odds':'<?=$school['odds']?>'},(resp)=>{
            comm.Alert(resp.msg,resp.code);
            if (resp.code) load_plan_items();
        },'json');
    }
</script>