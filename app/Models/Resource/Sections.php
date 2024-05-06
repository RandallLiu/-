<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Sections extends \App\Models\BaseModel {
    protected $table = 'tzy_sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['province_id', 'year', 'type', 'type_name', 'batch', 'batch_name', 'level','level_name', 'num', 'rank', 'rank_range', 'score', 'ctlscore', 'total'];
}