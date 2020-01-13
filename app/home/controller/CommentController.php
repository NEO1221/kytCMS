<?php
/*
created by syob
*/
namespace home\controller;
use core\Controller;
class CommentController extends Controller {
    public function insert() {
        //接受数据, 验证合法性
        $data['a_id']      = $_POST['a_id'];
        $data['c_comment'] = $_POST['c_comment'];
        if (empty($data['c_comment']) || mb_strlen($data['c_comment'])>50) $this->back('评论不能为空, 长度不能超过50');
        //补充数据入库
        @session_start();
        $data['c_time'] =time();
        $data['u_id'] = $_SESSION['user']['id'];
        //插入数据库
       if(!((new \home\model\CommentModel())->autoInsert($data))) $this->back('评论成功');
       else $this->back('评论失败');
    }
}