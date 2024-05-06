<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Special extends \App\Models\BaseModel {
    protected $table = 'tzy_specials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['special_id', 'name', 'spcode', 'level1', 'level1_name', 'level2', 'level2_name', 'level3', 'level3_name', 'degree', 'limit_year', 'rank', 'boy_rate', 'girl_rate', 'salaryavg', 'tuition', 'status', 'views', 'description'];
}