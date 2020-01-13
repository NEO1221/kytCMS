<?php
/*
created by syob
*/
namespace home\controller;
use \core\Controller;
class IndexController extends Controller {
    public function index() {
        @session_start();
        //处理文章数据
        global $config;
        $cond       = array();
        $page_count = $config['home']['article_page_count'] ?? 8;
        $page       = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $a          = new \home\model\ArticleModel();
        if (isset($_POST['a_title'])) $cond['a_title'] = trim($_POST['a_title']);
        if (isset($_GET['c_id'])) $cond['c_id'] = (int)($_GET['c_id']);
        $art = $a->getAllArticles($cond, $page_count, $page);
        //处理分页数据
        $url    = URL . 'index.php';
        $counts = $a->getCounts($cond);
        //        $cond['p'] = 'home';
        //        $cond['c'] = 'index';
        //        $cond['a'] = 'index';
        $page_str = (\vendor\Page::clickPage($url, $counts, $page_count, $page, $cond, true));
        //处理分类,添加到session中
        if (!isset($_SESSION['categories'])) {
            $_SESSION['categories'] = (new \home\model\CategoryModel())->getAllCategories();
        }
        //返回一个一维数组, 包含了c_id 和对应数量
        $art_cat_num = $a->getCountsByCategory();
        //获取最近三条记录
        $news = $a->getNewsInfo();
        //获取友情链接
        $f_links = (new \home\model\FlinkModel())->getAll();
        $f_links_num = count($f_links);

        //向模板传递数据
        $this->assign('f_links',$f_links);
        $this->assign('f_links_num',$f_links_num);
        $this->assign('news', $news);
        $this->assign('art_cat_num', $art_cat_num);
        $this->assign('categories', $_SESSION['categories']);
        $this->assign('page_str', $page_str);
        $this->assign('art', $art);
        //检索条件
        $this->assign('cond', $cond);
        $this->display('blogShowList.html');
    }
    //显示数据
    public function detail() {
        $id = (int)$_GET['id'];
        //获取文章
        $article = (new \home\model\ArticleModel())->getById($id);
        if (!$article) $this->msg(0, '获取失败');
        //这里为什么要开启session
        @session_start();
        //查找该文章的评论
        $comments = (new \home\model\CommentModel())->getCommentsByArticle($id);
        //写入模板信息
        $this->assign('comments',$comments);
        $this->assign('article', $article);
        $this->display('blogShow.html');
    }
}