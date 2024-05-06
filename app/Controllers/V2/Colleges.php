<?php
namespace App\Controllers\V2;

use App\Models\Form;
use App\Services\odds;
use function Qiniu\thumbnail;

class Colleges extends BC {
    protected $db;

    function __construct() {
        $this->db = new \App\Models\Resource\Schools();
    }

    // 大学列表
    public function data() {
        $P = $this->U();
        $P['searchField'] = 'name,short,code_enroll,phone,school_phone';
        $BTATCHS = json_decode(file_get_contents(WRITEPATH."uploads/data/dic.batch.key.json"),true); // LibComp::get_dict_data('LBATCH');
        if ( $this->s_name() &&  $this->s_dir()) $this->db->orderBy($this->s_name(),$this->s_dir());
        $data = $this->db->select("$this->colums")->search($P)->asObject()
            ->pagination( $this->page() , $this->size());
        $db = new Form();
        foreach ( $data['data'] as $row ) {
            $batch_ids = $row->batch ? explode(',',$row->batch) : []; $b = [];
            foreach ( $batch_ids as $v ) $b[] = $BTATCHS[$v];
            $pro_score_data = $db->select("province_id,min(min) as min,min(min_section) section ,max(year) year")->from('tzy_schools_province_score',true)->where('school_id',$row->school_id)->groupBy(['province_id','min','year'])->findAll();
            $min_scores = [];
            foreach ( $pro_score_data as $it )  $min_scores[$it['province_id']] = $it;

            // 总招生计划人数
            $plan_data = $db->selectSum('num','nums')->from('tzy_schools_province_specials_plan',true)->where(['school_id'=>$row->school_id,'year'=>$this->year()])->first();
            // 双一流学科数
            $dual_class_count = $db->from('tzy_dual_class',true)->where('school_id',$row->school_id)->countAllResults();

            $row->pro_min_score = $min_scores;
            $row->batchs = $b;
            $row->plan_nums = $plan_data['nums']??0;
            $row->dual_class_nums = $dual_class_count??0;
            $row->code = substr($row->code,0,5);

            unset($row->batch,$row->school_id);
        }
        $data['year'] = $this->year();
        $data['province'] = session('kemu')['province'];
        return $this->toJson($data);
    }

    // 专业学校
    public function special_schools(){
        $P = $this->U();
        $special_id = $P['special_id']; unset($P['special_id']);
        $P['searchField'] = 'name,short,code_enroll,phone,school_phone';
        $data = $this->db->select($this->colums)->search($P)->asObject()
            ->where("school_id in (select school_id from tzy_schools_specials where special_id=(select special_id from tzy_specials where id='{$special_id}'))")
            ->pagination($this->page() , $this->size());
        return $this->toJson($data);
    }

    // 学校列表
    public function index() {
        return $this->display([
            'view_path' => '/Schools/index',
        ]);
    }

    // 通过查找专业找学校
    public function search_specials(){
        $P = $this->U(); $year = $this->year(); $db = new \App\Models\Resource\SchSpecial();
        $P['searchField'] = 'b.special_name,b.level2_name,b.level3_name';

        if ( $P['favorite'] ) {
            $uid = session("id");
            $db->where("b.id in (select relate_id from favorite where user_id='$uid' and relate_type='school_special')");
        }

        $data = $db->select("a.id,b.id as sch_spe_id,a.school_id,name,belong,province_name,city_name,f211,f985,doublehigh,dual_class_name,school_nature_name as nature_name ,b.special_id,special_name,limit_year,b.type_name as stname,")
            ->from("tzy_schools as a",true)
            ->join("tzy_schools_specials as b","a.school_id = b.school_id")
            ->search($P)->pagination( $this->page() , $this->size());
        // log_message('error',$db->getLastQuery());
        $km = session("kemu");

        foreach ( $data['data'] as $item ) {
            $c = ["school_id"=>$item->school_id,"special_id"=>$item->special_id,"province" => $km["province"]?:"11","year" => $year];
            // 省招专业
            $special = $this->db->from("tzy_schools_province_specials",true)->where($c)->first();
            // 招生计划
            $plan = $this->db->from("tzy_schools_province_specials_plan",true)->where($c)->first();
            // 是否收藏
            $fav = $this->db->from("favorite",true)->where(["relate_id"=>$item->sch_spe_id,"user_id"=>session('id')??0])->first();

            $item->batch_name = $special['local_batch_name']??$item->stname;
            $item->type_name = $special['type_name']??'-';
            $item->min = $special['min']??'-';
            $item->section = $special['min_section']??'-';
            $item->favorite = $fav ? 1 : 0;
            $item->nums = $plan["num"] ?? '-';
            unset($item->school_id,$item->special_id,$item->stname);
        }

        // 收藏数
        $data["fnum"] = $db->from("favorite",true)->where("user_id",session('id'))->countAllResults();

        return $this->toJson($data);
    }

    // 学校详情
    public function detail() {
        $Id = $this->U('id');
        if ( $school_data = $this->db->where('id',$Id)->first() ) {
            $data['detail'] = $school_data;
            // 双一流学科
            $dual_class_data = (new \App\Models\Resource\SchDualClass())->where('school_id',$school_data['school_id'])->findAll();
            // 开设专业
            $specials_data = (new \App\Models\Resource\SchSpecial())->where('school_id',$school_data['school_id'])->findAll();
            $data['dual_class_data'] = $dual_class_data;
            $data['specials_data'] = $specials_data;

            return $this->render([
                'view_path' => '/Schools/detail',
                'data' =>$data
            ]);
        }
        return $this->setError('获取数据失败');
    }

    // 省控分数线
    public function province_score(){
        if ( $school_data = $this->db->where('id',$this->U('id'))->first() ) {
            $province_score_data = (new \App\Models\Resource\SchProScore())
                ->select("province_id as pro_code,type_name as type,local_batch_name as batch,min,min_section as section,year, zslx_name as zs")
                ->orderBy('year','desc')
                ->orderBy('pro_code','asc')
                ->where('school_id',$school_data['school_id'])->findAll();
            return $this->toJson(['data' => $province_score_data]);
        }
        return $this->setError('error');
    }

    // 省分数线
    public function province_special_score(){
        if ( $school_data = $this->db->where('id',$this->U('id'))->first() ) {
            $special_score_data = (new \App\Models\Resource\SchSpeScore())
                ->select("ifnull(b.name,a.spname) as name,province as pro_code,type_name as type,local_batch_name as batch,min,min_section as section,year,zslx_name as zs")
                ->from("tzy_schools_province_specials as a",true)
                ->join("tzy_specials as b",'a.special_id=b.special_id','left')
                ->orderBy('year','desc')
                ->orderBy('pro_code','asc')
                ->where('a.school_id',$school_data['school_id'])->findAll();
            return $this->toJson(['data' => $special_score_data]);
        }
        return $this->setError('error');
    }

    // 招生计划
    public function province_plan_score(){
        if ( $school_data = $this->db->where('id',$this->U('id'))->first() ) {
            $special_score_data = (new \App\Models\Resource\SchSpePlan())
                ->select("
                ifnull(b.name,a.spname) as name,
                a.spcode as scode,
                a.tuition,
                a.num,
                province as pro_code,
                type_name as type,
                local_batch_name as batch,
                a.limit_year as school_year,
                year,zslx_name as zs")
                ->from("tzy_schools_province_specials_plan as a",true)
                ->join("tzy_specials as b",'a.special_id=b.special_id','left')
                ->orderBy('year','desc')
                ->orderBy('pro_code','asc')
                ->where('a.school_id',$school_data['school_id'])->findAll();
            return $this->toJson(['data' => $special_score_data]);
        }
        return $this->setError('error');
    }

    // 设置收藏
    public function school_special_favorite(){
        $this->check_login_vip(true);
        $resp = favorite($this->U('id'),"school_special");
        if ( $resp ) return $this->toJson("操作成功!");
        return $this->setError("收藏失败,请登录进行收藏!");
    }

    // 模拟填报
    public function suggest(){
        $year = $this->year();
        $data["years"] = [$year,$year-1,$year-2];
        return $this->display(['view_path' => '/Suggest/index','data' => $data]);
    }

    // 模拟填报数据列表
    public function suggest_data(){
        // 用户科目
        $kemu = session('kemu'); $uid = session('id'); $P = $this->U(); $year = $this->year(); $self_score = $kemu['score'];
        // 院校或专业
        $chose = $P['chose']; $type = $P['score_type']; $batch = $kemu['batch']; $level = $P['level'];  $st = $P['type']; $province = $P['province'];unset($P['level'],$P['type'],$P['province']);
        // 冲击
        $down_score = 0; $up_score = 16; $special_sql = "";

        // 本科批选择 专业批次
        if ( $level == '2002' && $batch != '10') {
            /*  临界情况(废除)*/
            $section_db = (new \App\Models\Resource\Sections());
            // 位次条件
            $section_where = ['year' => $year, 'province_id' => $kemu['province'], 'type' => $kemu['typeId']];
            // 位次
            $section = $section_db->where($section_where)->where(['score' => $kemu["score"]])->first();

            // 获取本科最小分数值
            $score_batch_section = $section_db->selectMax("score")
                ->where("batch",10)
                ->where($section_where)->first();
            log_message('error',$section_db->getLastQuery());

            $temp_section = $section_db->where($section_where)->where('score', ($score_batch_section['score']))->first();
            log_message('error',$section_db->getLastQuery());

            $batch = $temp_section['batch'];
            log_message('error',"type:".$type);

            $self_score = $score_batch_section['score'];
            // 稳妥
            if ( (($kemu['score'] - $score_batch_section['score']) < 10) && $type == 'W') {

            }
        }
        // 院校优先
        if ( $chose == 'schools' ) {
            $this->db->search($P)->whereIn('b.type',$st);
            if ($province) $this->db->whereIn('b.province_id',$province);
        }

        // 专业优先
        if ( $chose == 'special' && ($P['level2_id'] || $P['level3_code'])) {
            if ( $P['level2_id'] ) $where = "where level2_id = '{$P['level2_id']}'";
            if ( $P['level3_code'] ) $where .= " and level3_code = '{$P['level3_code']}'";

            $special_sql = "(b.school_id in (select school_id from tzy_schools_specials $where))";
            $this->db->where("$special_sql");
        }

        // 稳妥
        if ($type == 'W') {
            $down_score = -18; $up_score = 0;
        }

        // 保底
        if ($type == 'B') {
            $down_score = -40; $up_score = -18;
        }

        // 最低(高)分值
        $min = $self_score + $down_score; $max = $self_score + $up_score;

        $search_data = ['a.batch' => $batch,"a.type"=>$kemu['typeId'],'a.province_id'=>$kemu['province'],'a.year'=>$this->year()];

        $db = new \App\Models\Resource\Schools();
        // 冲击 数量
        $cj_db = $this->_where($db,$search_data,$P,$st,$chose,$special_sql);  $mi = $self_score + 0 ; $ma = $self_score + 16;
        $chongji = $cj_db->where("(a.min >=$mi and a.min <= $ma)")->countAllResults();

        // 稳妥 数量
        $wt_db = $this->_where($db,$search_data,$P,$st,$chose,$special_sql);   $mi = $self_score - 18 ; $ma = $self_score + 0;
        $wt = $wt_db->where("(a.min >= $mi and a.min <= $ma )")->countAllResults();

        // 保底 数量
        $bd_db = $this->_where($db,$search_data,$P,$st,$chose,$special_sql);  $mi = $self_score - 40 ; $ma = $self_score - 18;
        $bd = $bd_db->where("(a.min >=$mi and a.min <= $ma )")->countAllResults();

        $data = $this->db->select("b.id,b.school_id,b.name,a.province_id, b.province_name as province, b.type_name as school_type,b.nature_name,b.admissions,b.f211,b.f985,b.doublehigh,b.dual_class_name,
        a.year,a.min,a.proscore,a.min_section as section")
            ->from('tzy_schools_province_score as a',true)
            ->join("tzy_schools as b","a.school_id=b.school_id")
            ->where($search_data)
            ->where("(a.min >=$min and a.min <= $max)")
            ->orderBy('a.min','asc')
            ->pagination( $this->page() , $this->size());

        log_message('error',$this->db->getLastQuery());

        foreach ( $data['data'] as $k=>$row ) {
            // 历年
            $score_data = $this->db->select("year,min,proscore,min_section as section")->from("tzy_schools_province_score",true)
                ->where(['school_id'=>$row->school_id,"province_id"=>$row->province_id,'type'=>$kemu['typeId'],'batch'=>$batch,//$kemu['batch']
                ])
                ->whereIn("year",[$year-1,$year-2])
                ->orderBy("year",'desc')->findAll();

            // 历年招生计划
            $plan_data = $this->db->select("year,sum(num) as num")->from("tzy_schools_province_specials_plan",true)
                ->where(['school_id'=>$row->school_id,"province"=>$row->province_id,'type'=>$kemu['typeId'],'batch'=>$batch,//$kemu['batch']
                ])
                ->whereIn("year",[$year-1,$year-2])
                ->orderBy("year",'desc')
                ->groupBy("year")
                ->findAll();

            // 最近一年招生计划
            $last_plan_data = $this->db->select("year,sum(num) as num")->from("tzy_schools_province_specials_plan",true)
                ->where(['school_id'=>$row->school_id,"province"=>$row->province_id,'type'=>$kemu['typeId']])
                ->where("year",$year)
                ->where('batch',$batch)
                ->orderBy("year",'desc')
                ->groupBy("year")
                ->findAll();

            $row->odds = 1;
            $row->scores = $score_data;
            $row->plan = $plan_data;
            $row->last_plan = $last_plan_data;
            $row->specials = 0;
            unset($row->school_id);
        }
        $data['tab']['cj'] = $chongji;
        $data['tab']['wt'] = $wt;
        $data['tab']['bd'] = $bd;
        return $this->toJson($data);
    }

    // 测试
    public function suggest_test(){
        $db = new \App\Models\Resource\Odds();
        //
        $odds_service = new odds();

        $P = $this->U();
        // 学校ID集合
        $shool_ids = []; $shool_key_position = [];
        // 用户科目 用户信息
        $kemu = session('kemu'); $uid = session('id');
        // 当年高考信息
        $year = ($this->year()); $score = $kemu['score'];
        // 院校或专业
        $chose = $P['chose']; $type = $P['score_type'];  $level = $P['level'];  $st = $P['type']; $province = $P['province'];unset($P['level'],$P['type'],$P['province']);
        //
        $special_sql = '';
        // 获取概率
        $position = odds_data(['level' => $level],$year);
        $odds_service->Avalue = $position['Avalue'];
        $shool_key_position = $position['shool_key_position'];
        $batch = $position['batch']; $score = $position['score'];


        // 院校优先
        if ( $chose == 'schools' ) {
            $this->db->search($P)->whereIn('b.type',$st);
            if ($province) $this->db->whereIn('b.province_id',$province);
        }

        // 获取分值范围
        if ($type) { $down_score = $score + ($this->cwb_range[$type]['min']/100 * $odds_service->Avalue); $up_score = $score + (($this->cwb_range[$type]['max']/100) * $odds_service->Avalue);}

        // 专业优先
        if ( $chose == 'special' && ($P['level2_id'] || $P['level3_code'])) {
            if ( $P['level2_id'] ) $where = "where level2_id = '{$P['level2_id']}'";
            if ( $P['level3_code'] ) $where .= " and level3_code = '{$P['level3_code']}'";
            $special_sql = "(b.school_id in (select school_id from tzy_schools_specials $where))";
            $this->db->where("$special_sql");
        }

        // 查询条件
        $search_data = ['a.batch' => $batch,"a.type"=>$kemu['typeId'],'a.province'=>$kemu['province'],'a.year'=>($year+1)];

        // 冲击 数量
        $chongji = $this->_tab_nums($province,$search_data,$P,$st,$chose,$special_sql,'C',$score,$shool_ids , $odds_service->Avalue);
        // 稳妥 数量
        $wt = $this->_tab_nums($province,$search_data,$P,$st,$chose,$special_sql,'W',$score,$shool_ids, $odds_service->Avalue);
        // 保底 数量
        $bd = $this->_tab_nums($province,$search_data,$P,$st,$chose,$special_sql,'B',$score,$shool_ids, $odds_service->Avalue);

        // 存在记录
        if ( $shool_ids ) $this->db->whereIn('a.school_id',$shool_ids);
        // 取值范围
        if ( $type ) $this->db->where("(Avalue > ($down_score-proscore) and Avalue < ($up_score-proscore))");

        $data = $this->db->select("b.id,b.school_id,b.name,a.province as province_id, b.province_name, 
            b.type_name as school_type,b.nature_name,b.admissions,b.f211,b.f985,b.doublehigh,b.dual_class_name,
            a.year,a.min_score,a.proscore,a.min_section as section, a.id as odds_id")
            ->from("tzy_odds_score as a",true )
            ->join("tzy_schools as b","a.school_id = b.school_id" )
            ->where($search_data)
            ->orderBy('a.Avalue','desc')
            ->pagination( $this->page() , $this->size());

        log_message('error',$this->db->getLastQuery());
        // 查出所有可能情况
        // $all_nums = count($shool_ids);

        foreach ( $data['data'] as $row ) {
            // 历年
            $score_data = $this->db->select("year,min,proscore,min_section as section")->from("tzy_schools_province_score",true)
                ->where(['school_id'=>$row->school_id,"province_id"=>$row->province_id,'type'=>$kemu['typeId'],'batch'=>$batch,])
                ->whereIn("year",[$year-1,$year-2])
                ->orderBy("year",'desc')->findAll();

            // 历年招生计划
            $plan_data = $this->db->select("year,sum(num) as num")->from("tzy_schools_province_specials_plan",true)
                ->where(['school_id'=>$row->school_id,"province"=>$row->province_id,'type'=>$kemu['typeId'],'batch'=>$batch,//$kemu['batch']
                ])
                ->whereIn("year",[$year-1,$year-2])
                ->orderBy("year",'desc')
                ->groupBy("year")
                ->findAll();

            // 最近一年招生计划
            $last_plan_data = $this->db->select("year,sum(num) as num")->from("tzy_schools_province_specials_plan",true)
                ->where(['school_id'=>$row->school_id,"province"=>$row->province_id,'type'=>$kemu['typeId']])
                ->where("year",$year)
                ->where('batch',$batch)
                ->orderBy("year",'desc')
                ->groupBy("year")
                ->findAll();

            $odds_service->odds_type = $type;
            // 当前所处位置
            $odds_service->schAvalue = $shool_key_position[$row->school_id]['value'];

            // 录取概率
            $row->odds = $odds_service->compares_avalue();
            $row->scores = $score_data;
            $row->plan = $plan_data;
            $row->last_plan = $last_plan_data;
            $row->specials = 0;
            $row->year = $year;
            $row->t = $odds_service->odds_type;
            $row->tn = $row->odds < 10 ? "风险很大" : $this->cwb[$odds_service->odds_type];
            unset($row->school_id);
        }

        $data['tab']['cj'] = $chongji;
        $data['tab']['wt'] = $wt;
        $data['tab']['bd'] = $bd;

        return $this->toJson($data);
    }

    // 模拟填报详情
    public function report(){
        $odds_service = new odds();
        $P = $this->U(); $year = $this->year(); $kemu = session('kemu'); $years = [$year,$year-1,$year-2];
        // 学校数据
        if ($school_data = $this->db->asObject()->where("id",$P['id'])->first()) {
            $argc = ['school_id' => $school_data->school_id , 'type' => $kemu['typeId'],'batch' => $P['level'] == '2001' ? 14 : 10];

            $position = odds_data($P,$year);
            $odds_service->odds_type = $P['score_type'];
            // 平均分差
            $odds_service->Avalue = $position['Avalue'];
            // 当前所处位置
            $odds_service->schAvalue = $position['shool_key_position'][$school_data->school_id]['value'];
            //
            $school_data->odds = $odds_service->compares_avalue();

            // 学校所有省份分数线
            $school_data->total_score_data = $this->db->from("tzy_schools_province_score",true)
                ->whereIn('year',$years)->where($argc)
                ->where('province_id',$kemu['province'])->findAll();

            // 学校所有招生计划数据
            $school_data->total_plan_data = $this->db->select("year,sum(num) as nums")
                ->from("tzy_schools_province_specials_plan",true)->whereIn('year',$years)
                ->where('province',$kemu['province'])->where($argc)
                ->orderBy("year",'desc')
                ->groupBy("year")
                ->findAll();
            // 志愿卡专业数
            $odds_data = (new \App\Models\Resource\Odds())->where('id',$P['odds_id'])->first();
        }

        $P['batch'] = $argc['batch'];
        $data["school"] = $school_data;
        $data["years"] = $years;
        $data["argc"] = $P;
        return $this->render(['view_path' => '/Suggest/detail','data' => $data]);
    }

    // 加载计划项
    public function plan_item(){
        $plan_db = (new \App\Models\Resource\SchSpePlan());  $odds_service = new odds();
        $P = $this->U(); $year = $this->year(); $kemu = session('kemu');

        $years = [$year,$year-1,$year-2]; $plan_data = []; $score_data = [];
        // 学校数据
        if ($school_data = $this->db->asObject()->where("id",$P['id'])->first()) {
            // 学校录取机率表
            $odds_data = (new \App\Models\Resource\Odds())->where('id',$P['odds_id'])->first();
            //
            $position = odds_data($P , $year);
            // $odds_service->odds_type = $P['stype'];
            $odds_service->Avalue = $position['Avalue'];

            // var_dump($odds_service->odds_type,$odds_service->Avalue);
            //
            $argc = ['school_id' => $school_data->school_id , 'type' => $kemu['typeId'],'batch' => $P['level'] == '2001' ? 14 : 10];
            // 历年分数线
            $score_data = $this->db->from("tzy_schools_province_specials",true)->where($argc)->where('province',$kemu['province'])->whereIn('year',$years)->findAll();

            // 历年招生计划
            $plan_data = $plan_db->asObject()
                ->select("a.* , b.name as spname2")
                ->from("tzy_schools_province_specials_plan as a",true)
                ->join("tzy_specials as b", "a.special_id = b.special_id","left")
                ->where($argc)->where('a.province',$kemu['province'])->whereIn('a.year',$years)->findAll();

            $asp_db = (new \App\Models\Resource\Aspiration());

            // 判断是否存在志愿卡
            foreach ( $plan_data as $plan ) {
                $has_asp = $asp_db->where("plan_id",$plan->id)->first();
                $plan->has_plan = $has_asp ? true : false;
                $odds = 0; $odds_type = '';
                foreach ($score_data as $score) {
                    if ($plan->special_id == $score['special_id'] && ($plan->year == $score['year'] && $year == $score['year'] && $year == $plan->year)) {
                        $s = (((is_numeric($score['average']) && $score['average'])?$score['average']: $score['min']) - $odds_data['proscore']);
                        $odds_service->schAvalue = $s;
                        $odds = $odds_service->compares_avalue();
                    }
                }
                $plan->odds = $odds;
            }
        }

        $data["school"] = $school_data;
        $data["years"] = $years;
        $data["plans"] = $plan_data;
        $data["scores"] = $score_data;
        $data["argc"] = $P;
        return $this->render([
            'view_path' => '/Suggest/plan','data' => $data
        ]);
    }

    // 条件
    private function _where($db , $argc , $search , $type , $chose, $special_sql ){
        if ( $chose == 'schools' ) $db->search($search)->whereIn('b.type',$type);
        if ( $chose == 'special' && $special_sql )  $db->where("$special_sql");
        return $db->from('tzy_odds_score as a',true)->join("tzy_schools as b","a.school_id=b.school_id")->where($argc);
    }

    // 标签数量
    private function _tab_nums($prvince, $search_data, $P, $st, $chose, $special_sql, $cwb ,$score , $shool_ids , $aValue) {
        $db = new \App\Models\Resource\Schools();
        $dw = ($this->cwb_range[$cwb]['min'] * $aValue)/100;  $us = ($this->cwb_range[$cwb]['max']*$aValue)/100;
        $db = $this->_where($db,$search_data,$P,$st,$chose,$special_sql); $down = ($score + $dw); $up = ($score + $us);
        if ( $shool_ids ) $db->whereIn('a.school_id',$shool_ids);
        if ($prvince) $db->whereIn('b.province_id',$prvince);
        $nums = $db->where("(Avalue > ($down-proscore) and Avalue < ($up-proscore))")->countAllResults();
        return $nums;
    }

    // 计算概率
    private function _calc_type($score , $proscore , $Avalue) {

    }



    private $colums = "id,
            school_id,
            icon,
            name,
            code_enroll as code,
            phone,
            school_phone as tel_phone,
            email,
            province_name as province,
            city_name as city,
            belong,
            admissions as qiangji,
            doublehigh,
            f211 as is211,
            f985 as is985,
            dual_class_name as dual_name,
            level_name,
            type_name,
            school_nature_name as school_type ,
            ruanke_rank as rank,
            school_batch batch
            ";
}

