<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class ProScore extends \App\Models\BaseModel {
    protected $table = 'tzy_province_score';
    protected $primaryKey = 'id';
    protected $allowedFields = ['province_id', 'province', 'name', 'type', 'type_name', 'batch', 'batch_name', 'major_score', 'score_section', 'score', 'rank', 'year'];
}