<?php namespace App\Models;

use CodeIgniter\Model;

class Users extends \App\Models\BaseModel {
    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $allowedFields = ['name','phone','password','province','kemu','score'];

    protected $validationRules = [
        'phone'     => 'is_unique[users.phone,id,{id}]|min_length[11]',
    ];

    protected $validationMessages =[
        'phone'=>['is_unique'     => '手机号已存在','min_length'    => '手机号长度不够'],
    ];
}
