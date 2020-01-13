<?php
/*
created by syob
*/
namespace admin\controller;
use admin\model\CommentModel;
use core\Controller;
class commentController extends Controller {
    public function index(){
        //获取参数列表
        $c = new \admin\model\CommentModel();
        $url = URL."index.php";
        $counts = $c->getAllNum();
        global $config;
        $page_count = $config['admin']['comment_page_count'] ?? 2;
        $page = isset($_GET['page'])? $_GET['page'] : 1;
        $cond = array('p'=>'admin','c'=>'comment','a'=>'index');
        //获取角码
        $page_str = \vendor\Page::ClickPage($url,$counts,$page_count,$page,$cond,false);
        //写入分页的展现内容
        $comments = $c->getAllComments($page_count,$page);

        $this->assign('page_str',$page_str);
        $this->assign('comments',$comments);
        $this->display('commentIndex.html');
    }
    //删除指定留言
    public function delete(){
        $id = $_GET['id'];
        if(!(new \admin\model\CommentModel())->deleteById($id)) $this->back('删除成功');
        else $this->back('删除失败');
    }
}