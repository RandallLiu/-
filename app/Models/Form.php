<?php namespace App\Models;

use CodeIgniter\Model;

class Form extends \App\Models\BaseModel {
    protected $primaryKey = 'id';
    protected $table = 'form';
    protected $allowedFields = ['guid','status'];
}
