<?php
namespace App\Controllers\V2;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use App\Services\comm;
use CodeIgniter\Controller;
use Config\Services;

class BC extends \App\Controllers\Base {

    protected $cwb = ['N' => '风险较大','C' => '冲','W' => '稳' , 'B' => '保'];

    protected $cwb_range = [
        'N' => ['min'=>20,'max'=>''],
        'C' => ['min'=>0,'max'=>20],
        'W' => ['min'=>-20,'max'=>0],
        'B' => ['min'=>-80,'max'=>-20],
    ];

    /**
     * Session 设置
     */

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        // 记录
        (new \App\Services\opertions())->log($this->U());
    }

    // 分頁码
    protected function page(){
        $page = $this->request('iDisplayStart');
        $size = $this->request('iDisplayLength');
        return intval(($page?:0)/($size?:1))+1;
    }
    // 分页大小
    protected function size(){
        $size = $this->U('iDisplayLength');
        return $size?:10;
    }
    // 排序字段
    protected function s_name(){
        $sort_index = $this->request('iSortCol_0');
        $filed = $this->U('mDataProp_'.strval($sort_index));
        return $filed;
    }
    // 排序方向
    protected function s_dir(){
        $s_dir = $this->request('sSortDir_0');
        return $s_dir?:'asc';
    }
    // 获取年份
    public function year(){
        $current_year = date('Y');
        $current_month = intval(date('m'));
        if ( $current_month < 9 ) {
            return ($current_year - 1) ;
        }
        return $current_year;
    }
}