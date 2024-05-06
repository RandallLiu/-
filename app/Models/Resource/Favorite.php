<?php namespace App\Models\Resource;

use CodeIgniter\Model;

class Favorite extends \App\Models\BaseModel {
    protected $table = 'favorite';
    protected $primaryKey = 'id';
    protected $allowedFields = ['relate_id', 'user_id', 'relate_type'];
}