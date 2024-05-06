<?php
namespace App\Entities;

use CodeIgniter\Entity;

class Menus extends Entity{
    protected $casts = [
        'isLink' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
        'isKeepAlive' => 'integer',
        'isAffix' => 'integer',
        'isIframe' => 'integer',
        'hidden' => 'integer',
        'parentid' =>'integer'
    ];
}