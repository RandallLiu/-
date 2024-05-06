<?php
namespace App\Controllers\V2;

class Aspiration extends BC
{
    protected $db;
    function __construct() {
        $this->db = new \App\Models\Resource\Aspiration();
    }

    // 我的志愿卡
    function mycard(){
        $this->NLogin = true;
        $year = $this->year(); $self_kemu = session('kemu');
        $years = [$year,$year-1,$year-2];
        $km = session('kemu');
        $argc = ['user_id' => session('id'),'type'=>$km['typeId'],'batch'=>$km['batch']];
        // 冲击
        $data["cj"] =  $this->db->where($argc)->whereIn("score_type",["C",'N'])->countAllResults();
        // 稳妥
        $data["wt"] =  $this->db->where($argc)->where("score_type","W")->countAllResults();
        // 保底
        $data["bd"] =  $this->db->where($argc)->where("score_type","B")->countAllResults();

        $data["years"] = $years;
        $data["kemu"] = $self_kemu;
        return $this->display(['view_path' => '/Aspiration/mycard','data' => $data]);
    }

    // 加载志愿卡数据
    function load_card(){
        $this->check_login_vip(true);
        $db = new \App\Models\Resource\Schools();
        $userId = session('id'); $year = $this->year();
        $years = [$year,$year-1,$year-2]; $pattern = "/^[^（]+/";
        $card_data = [];  $km = session('kemu'); $st = $this->U('score_type');
        // 学校列表
        $school_data = $db->where("school_id in (select distinct school_id from aspiration where user_id = $userId and type = '{$km['typeId']}' and batch = '{$km['batch']}' and score_type='$st')")->findAll();
        // 循环
        foreach ( $school_data as $school ) {
            // 获取志愿信息
            $cards = $db->from("aspiration",true)->where(["school_id"=>$school['school_id'],'user_id' => $userId,'type'=>$km['typeId'],'batch'=>$km['batch'],'score_type'=>$st])->orderBy("createtime","desc")->findAll();
            $kemu = $km; $children = []; $plans = []; $scores = []; $argc = []; $sch_odds = 0;
            //
            foreach ( $cards as $k=>$card ) {
                if ($k == 0) {
                    $argc = ['school_id' => $school['school_id'], 'type' => $card['type'], 'batch' => $card['batch']]; $sch_odds = $card['sch_odds'];
                }

                // 当前专业招生计划
                $current_plan = $db->select("a.*, b.name as spname2")
                    ->from("tzy_schools_province_specials_plan as a",true)
                    ->join("tzy_specials as b","a.special_id = b.special_id","left")
                    ->where('a.id',$card['plan_id'])->first();

                // 近3年招生计划
                $temp_plans_data = $db->select("year,num,special_id,sp_info")->from("tzy_schools_province_specials_plan",true)
                    ->whereIn('year',$years)->where('special_id',$current_plan['special_id'])
                    ->where("province",$kemu['province'])
                    ->where($argc)->findAll();

                // 近3年专业分数
                $temp_score_data = $db->select("year,special_id,min,min_section,sg_info")
                    ->from("tzy_schools_province_specials",true)->where($argc)
                    ->where('special_id',$current_plan['special_id'])
                    ->where('province',$kemu['province'])
                    ->whereIn("year",$years)->findAll();

                // 截取专业长度
                preg_match($pattern, $card['spname'], $matches);

                $children[] = [
                    'id' => $card['plan_id'],
                    'special_id' => $current_plan['special_id'],
                    'province' => $current_plan['province'],
                    'num' => $current_plan['num'],
                    'spname' => $current_plan['spname2']?:$matches[0],
                    'spcode' => $current_plan['spcode'],
                    'tuition' => $current_plan['tuition'],
                    'limit_year' => $current_plan['limit_year'],
                    'sp_info' => $current_plan['sp_info'],
                    'plans' => $temp_plans_data,
                    'scores' => $temp_score_data,
                    'odds' => $card['odds'],
                ];

            }

            // 省份
            $province = $kemu ? $kemu['province']: '';

            // 近3年省招生计划
            $province_plans = $db->select("year,sum(num) nums")->from("tzy_schools_province_specials_plan",true)
                ->where($argc)->where('province' ,$province)->whereIn('year',$years)->groupBy("year")->findAll();

            // 近3年省分数线及最低位次
            $province_scores = $db->select("min,min_section,sg_info,year")->from("tzy_schools_province_score",true)
                ->whereIn('year',$years)->where('province_id',$province)->where($argc)->findAll();

            $card_data[] = [
                'school_id' => $school['school_id'],
                'name' => $school['name'],
                'odds'=>$sch_odds,
                'code_enroll'=> $school['code_enroll'],
                'province' => $school['province_name'] ,
                'school_type' => $school['type_name'] ,
                'nature_name' => $school['nature_name'],
                'admissions' => $school['admissions'] == 1 ? '强基计划': '',
                'f211' => $school['f211'] == 1 ? '211' : '',
                'f985' => $school['f985'] == 1 ? '985' : '',
                'doublehigh' => $school['doublehigh'] == 1 ? '双高计划' : '',
                'dual_class_name' => $school['dual_class_name'],
                'his_province_province_plans' => $province_plans,
                'his_province_scores' => $province_scores,
                'children' => $children
            ];
        }

        $data["cards"] = $card_data;

        $data["years"] = $years;

        return $this->render(['view_path' => '/Aspiration/card','data' => $data]);
    }

    // 保存志愿
    public function save(){
        $this->check_login_vip(true);
        $plan_db = new \App\Models\Resource\SchSpePlan(); $P = $this->U();
        // 判断是否存在记录
        if ( ($plan_data = $plan_db->where(['id' => $P['id']])->first()) && (!$this->db->where("plan_id",$P['id'])->first())){
            $kemu = session('kemu');
            $special_data = $plan_db->from('tzy_schools_province_specials',true)->where(['school_id'=>$plan_data['school_id'],'province'=>$plan_data['province'],'type'=>$plan_data['type'],'batch' => $plan_data['batch'],'special_id' => $plan_data['special_id']])->first();
            if (session('id') && $this->db->save(['plan_id' => $P['id'],
                    'score_type' => $P['score_type'],
                    'odds' => $P['odds'],
                    'sch_odds'=>$P['sch_odds'],
                    'odds_id' => $P['odds_id'],
                    'score' => $kemu['score'],
                    'min' => $special_data['min'],
                    'average' => $special_data['average'],
                    'type' => $kemu['typeId'],'batch' => $P['batch'],
                    'kemu' => json_encode($kemu,JSON_UNESCAPED_UNICODE),'user_id' => session('id'),'school_id' => $plan_data['school_id']])) {
                // log_message('error', $this->db->getLastQuery());
                return $this->toJson("模拟填报保存成功!");
            }
        }
        return $this->setError("模拟填报保存失败!");
    }

    // 删除单个志愿
    public function delete() {
        $this->check_login_vip(true);
        if ( $this->db->where(['plan_id'=>$this->U('id'),'user_id'=>session('id')])->delete() ) {
            // log_message('error',$this->db->getLastQuery());
            return $this->toJson("模拟填报删除成功!");
        }
        return $this->setError("删除失败");
    }

    // 删除志愿卡
    public function delete_card(){
        $this->check_login_vip(true);
        if ( $this->db->where(['school_id'=>$this->U('id'),'user_id'=>session('id')])->delete() ) {
            return $this->toJson("志愿删除成功!");
        }
        return $this->setError("删除失败");
    }
}