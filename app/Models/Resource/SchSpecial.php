<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class SchSpecial extends \App\Models\BaseModel {
    protected $table = 'tzy_schools_specials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id','name', 'special_id', 'code', 'special_name', 'special_type', 'type_name', 'level2_name', 'level2_id', 'level2_code', 'level3_name', 'level3_code', 'limit_year', 'year', 'status'];
}