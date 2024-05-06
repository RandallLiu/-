<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;

class LoginLogs extends \App\Models\BaseModel
{
    protected $table = 'admin_login_logs';

    protected $primaryKey = 'id';

    protected $allowedFields = ['userId','username','ip','ua'];
}