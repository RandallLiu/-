<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class SchSpePlan extends \App\Models\BaseModel {
    protected $table = 'tzy_schools_province_specials_plan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id', 'special_id', 'province', 'type', 'type_name', 'batch', 'local_batch_name', 'spname', 'spcode', 'tuition', 'limit_year', 'num', 'zslx_name', 'level1_name', 'level2_name', 'level3_name', 'level1', 'level2', 'level3', 'year', 'sp_xuanke', 'sp_info', 'sp_type', 'sp_sxk', 'special_group'];
}