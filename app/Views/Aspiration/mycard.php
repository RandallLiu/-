<?php
$cards = $data['cards']; $current_year  = $data['years'][0]; $sp_info = ""; $kemu = $data['kemu'];
$km = [];

foreach ( $kemu['kemu'] as $v ) {
    $km[] = mb_substr(\App\Libraries\LibComm::$ksxk[$v],0,1);
}
?>
<style>
    .main-tr {
        /*background-color: #f2f8ed*/
    }
    h4, .h4, h5, .h5, h6, .h6{margin-top: 2px; margin-bottom: 2px;}
    .card-list p{margin-bottom: 0px;padding: 5px 12px;}
    .card-list thead th{position: sticky;background-color: #d4d4d4;top: 45px; z-index: 999; border-top:1px solid #e2e2e2;padding: 12px 20px }
</style>

<div class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-flat"  id="release">
                <div class="panel-body" style="padding: 20px 20px 10px 20px">
<!--                    <a href="/home/test" class="btn bg-indigo-400" style="position: absolute; right: 20px" onclick="return comm.doRequest(this.href,{},(resp)=>{console.log('resp:',resp)})"><i class="icon  icon-plus3"></i> 添加志愿卡 </a>-->

                    <span class="text-muted" style="position: absolute; left: 220px;padding: 6px">
                        <?=$kemu['province_name']??App\Libraries\LibComm::$province[$kemu['province']]?> · <?=\App\Libraries\LibComm::$batch[$kemu['batch']]?> · <?=($kemu['type_name']??\App\Libraries\LibComm::$kemu[$kemu['typeId']])?> <?=$km?('('.join('/',$km).')'):''?> · <?=$kemu['score']?>
                    </span>
                    <input name="score_type" type="hidden" value="C">
                    <div class="tabbable frmb">
                        <ul class="nav nav-pills nav-pills-toolbar text-left nav-xs" style="margin-bottom: 0px">
                            <li class="active"><a href="#1" data-toggle="tab" data-status="C"> 冲击(<span class="cj"><?=$data['cj']?:0?></span>) </a></li>
                            <li class=""><a href="#1" data-toggle="tab" data-status="W"> 稳妥(<span class="wt"><?=$data['wt']?:0?></span>) </a></li>
                            <li class=""><a href="#1" data-toggle="tab" data-status="B"> 保底(<span class="bd"><?=$data['bd']?:0?></span>) </a></li>
                        </ul>
                    </div>
                </div>
                    <div style="padding: 10px 20px">
                        <table class="table table-bordered card-list table-hover">
                            <thead>
                            <tr>
                                <th width="7%"></th>
                                <th width="23%">招生院校专业</th>
                                <th width="10%" class="text-center">录取概率</th>
                                <th width="10%" class="text-center"><?=$current_year?>招生计划</th>
                                <th width="10%" class="text-center">历年</th>
                                <?php foreach ($data['years'] as $year):?>
                                    <th width="10%" class="text-center"><?=$year?></th>
                                <?php endforeach;?>
                                <th width="10%" class="text-right">
                                    操作
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="9">暂无数据</td></tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function (){
        //data-spy="scroll" data-target=".sidebar-detached" class="has-detached-right"
        $("body").data("spy","scroll"); $("body").data("target",".sidebar-detached");
        $("body").addClass("has-detached-right");
        $('.frmb>ul>li>a').on('click',function () {
            var status = $(this).data('status'); $('input[name=score_type]').val(status);
            load_card();
        });
        load_card();
    });

    function expand_collapse(_this,id){
        var state = $(_this).data('state'),display = "none" , text = "展开专业" , state_text = "collapse" , icon = "icon icon-arrow-right22";
        if (state != 'expand') {
            display = ""; text = "折叠专业" ;state_text = "expand"; icon = "icon icon-arrow-down22";
        }
        $(`.${id}`).css("display", display);$(_this).data("state",state_text);$(_this).find("span.a").text(text);
    }

    function remove_card(s,id) {
        load_card()
    }

    function load_card(){
        var st = $('input[name=score_type]').val();
        comm.doRequest("/v2/aspiration/load_card",{'score_type':st},(resp)=>{
            $('table.card-list>tbody').html(resp?resp:`<tr><td colspan="9" class="text-center" style="padding:12px;">暂无数据</td></tr>`);
        });
    }
</script>