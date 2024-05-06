<?php
namespace App\Services;

use CodeIgniter\Model;

class odds {

    // 分差倍数
    public $cs = 5;
    // 录取概率初始值 百分比
    public $base_rate = 30;
    //
    public $feed_value = 15;

    // 分差高分区限制百分比
    public $up_percent = 30;
    // 中分区限制百分比
    public $mid_percent = 35;
    // 低分区限制百分比
    public $down_percent = 55;

    // 录取概率类型(C:冲击,W:稳妥, B:保底)
    public $odds_type = '';

    // 冲击
    public $odds_cj_rate = 1;
    // 低分(稳妥)区比例值
    public $odds_wt_rate = 1.2;
    // 低分(保底)区比例值
    public $odds_bd_rate = 1.4;

    // 当前考生分差值
    public $Avalue = 0;

    // 当前学校所在分差
    public $schAvalue = 0;


    public function __construct() {
    }

    // 比较3年平均分差
    function compares_avalue(){
        // 高分区
        if ( ($this->schAvalue - $this->Avalue) >= 0 ) {
            $u = round((($this->schAvalue - $this->Avalue) / $this->Avalue) * 100);
            // log_message('error','schAvalue:'.$this->schAvalue.',Avalue:'.$this->Avalue);
            $odds =  ($u >= $this->up_percent) ? -($this->up_percent - 1) : -$u;
            $this->odds_type = $this->odds_type ?:(($odds + $this->base_rate) < $this->feed_value ? 'N':'C');
            $bs = $this->base_rate;

        } else {
            // 低分区
            $b = ($this->Avalue - $this->schAvalue) <= $this->mid_percent ? $this->mid_percent: $this->down_percent;
            $d = (($this->Avalue - $this->schAvalue) / $this->Avalue) * 100;
            $down_rate = $d >= $this->mid_percent ? $b : $d;
            $odds = $down_rate ; $bs = $this->base_rate + $this->feed_value ;
            $this->odds_type = $this->odds_type?:($b == $this->mid_percent ? 'W' : 'B');
        }

        // 不同区域类型不同权重比例
        $rate = ($this->odds_type == 'W' ? $this->odds_wt_rate : ($this->odds_type == 'B'?$this->odds_bd_rate:$this->odds_cj_rate));
        // log_message('error',$odds);
        $odds = $bs * $rate  + round($odds);
        // log_message('error',$rate . '-'.$bs. '-'.$odds );
        $AB = (intval($odds / $this->cs) * $this->cs);

        return (($odds > $this->cs) && $odds > 20) ? ($AB > 99 ? 99: $AB) : $odds;
    }

    //  专业录取概率
    function specials_score($score, $Avalue ,$proscore){
        // 专业分差
        $special_value = ($score - $proscore);
        //
    }
}