<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class OperationsLog extends \App\Models\BaseModel {
    protected $table = 'admin_operation_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['userid','username','type','controller','action','uri','ip','source'];
    protected $returnType = 'App\Entities\Forms';
}