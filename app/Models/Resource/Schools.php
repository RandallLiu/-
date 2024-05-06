<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Schools extends \App\Models\BaseModel {
    protected $table = 'tzy_schools';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id', 'name', 'short', 'data_code', 'code_enroll', 'phone', 'school_phone', 'email','province_id', 'province_name', 'city_name','admissions', 'f211', 'f985','doublehigh','dual_class', 'dual_class_name', 'area', 'postcode', 'address','level', 'level_name','type', 'type_name','school_type', 'school_type_name','school_nature', 'school_nature_name', 'nature_name', 'school_site', 'site', 'ruanke_rank', 'icon', 'content', 'founddt', 'images', 'status', 'school_batch', 'fenxiao','create_date','belong','xyh_rank','qs_world','us_rank','num_doctor','num_master','num_subject','xueke_rank'];
}