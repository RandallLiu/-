<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Aspiration extends \App\Models\BaseModel {
    protected $table = 'aspiration';
    protected $primaryKey = 'id';
    protected $allowedFields = ['plan_id','score_type','odds', 'user_id','score', 'status','school_id','kemu','batch','type','odds_id','min','average','sch_odds'];
}