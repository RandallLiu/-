<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class SchSpeScore extends \App\Models\BaseModel {
    protected $table = 'tzy_schools_province_specials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id', 'special_id', 'province', 'type', 'type_name', 'batch', 'local_batch_name', 'spname', 'zslx', 'zslx_name', 'sg_type', 'sg_name', 'sg_info', 'sg_xuanke', 'sg_fxk', 'min', 'max', 'min_section', 'average', 'special_group', 'level1_name', 'level2_name', 'level3_name', 'level1', 'level2', 'level3', 'year'];
}