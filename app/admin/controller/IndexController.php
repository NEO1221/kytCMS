<?php
/*
created by syob
*/

namespace admin\controller;

use core\Controller;

class IndexController extends Controller {
    public function index() {


        $user   = new  \admin\model\UserModel();
        $counts = $user->getCounts();

        //批量增加数据,平时不用要注销
        //$user->add_info_test();

        $this->assign('counts', $counts);
        $this->display('index.html');
    }
}