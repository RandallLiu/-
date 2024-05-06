<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class SchDualClass extends \App\Models\BaseModel {
    protected $table = 'tzy_dual_class';
    protected $primaryKey = 'id';
    protected $allowedFields = ['school_id', 'class'];
}