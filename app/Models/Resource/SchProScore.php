<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class SchProScore extends \App\Models\BaseModel {
    protected $table = 'tzy_schools_province_score';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id', 'province_id', 'type', 'type_name', 'batch', 'local_batch_name', 'min', 'proscore', 'average', 'filing', 'min_section', 'max', 'sg_type', 'sg_name', 'sg_info', 'year', 'zslx_type', 'zslx_name'];
}