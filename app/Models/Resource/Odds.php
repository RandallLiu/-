<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Odds extends \App\Models\BaseModel {
    protected $table = 'tzy_odds_score';
    protected $primaryKey = 'id';
    protected $allowedFields = ['relate_id', 'relate_tb', 'province','type','batch','school_id','proscore','min_score','average','Avalue','Bvalue','Dvalue','avg_section','min_section','year'];
}