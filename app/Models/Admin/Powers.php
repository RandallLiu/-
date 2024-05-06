<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;

class Powers extends \App\Models\BaseModel
{
    protected $table = 'admin_power';
    protected $primaryKey = 'id';
    protected $allowedFields = ['menu_id','role_id','operation_id'];
    protected $returnType = 'App\Entities\Forms';
    
}