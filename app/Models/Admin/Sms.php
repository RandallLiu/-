<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;

class Sms extends \App\Models\BaseModel
{
    protected $table = 'verycode';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'phone', 'ipaddr', 'ua', 'type', 'status'];
}
