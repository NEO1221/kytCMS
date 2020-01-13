<?php
/*
created by syob
*/
namespace admin\controller;
use core\Controller;
class ArticleController extends Controller {
    public function index() {
        /******************************************************/
        //  写入测试数据时使用
//         (new \admin\model\ArticleModel())->add_info_test();
        /******************************************************/
        //设置分类, 缓存到session中
        if (isset($_SESSION['categories'])) {
            $_SESSION['categories'] = (new \admin\model\CategoryModel())->getAllCategories();
        }
        //顶一个条件数组,接受传进来的条件
        $cond = array();
        if (isset($_REQUEST['a_title']) && trim($_REQUEST['a_title'])) $cond['a_title'] = $_REQUEST['a_title'];
        if (isset($_REQUEST['c_id']) && (int)($_REQUEST['c_id'])) $cond['c_id'] = (int)$_REQUEST['c_id'];
        if (isset($_REQUEST['a_status']) && (int)($_REQUEST['a_status'])) $cond['a_status'] = (int)$_REQUEST['a_status'];
        if (isset($_REQUEST['a_toped']) && (int)($_REQUEST['a_toped'])) $cond['a_toped'] = (int)$_REQUEST['a_toped'];
        //如果不是管理员,再获取用户id
        if (!$_SESSION['user']['u_is_admin']) $cond['u_id'] = $_SESSION['user']['id'];
        //打印一份cond数组
        //没有任何条件时, cond数组为0
        //处理分页信息,$page为当前页码
        $page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1 ;
        global $config;
        //$pagecount 为配置每页的文章数量
        $pagecount = $config['admin']['article_page_count'] ?? 5;
        //建立模型对象
        $a      = new \admin\model\ArticleModel();
        //$art为一个,数量为每页文章数量的二维数组, 每个子数组下, 包含各个文章信息
        $art    = $a->getArticleInfo($cond, $pagecount, $page);
        //获取该条件下的, 所有文章的数量
        $counts = $a->getSearchCounts($cond);
        $cond['a'] = A;
        $cond['p'] = P;
        $cond['c'] = C;
        $pagestr   = \vendor\Page::clickPage(URL . 'index.php', $counts, $pagecount, $page, $cond,false);
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=1'>上一页</a></li>
        //<li><a style='color:#000;' href='http://blog.com/index.php?a=index&p=admin&c=article&page=1'>1</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=2'>2</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=3'>3</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=4'>4</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=5'>5</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=6'>6</a></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=7'>7</a></li>
        //<li><span>...</span></li>
        //<li><a href='http://blog.com/index.php?a=index&p=admin&c=article&page=2'>下一页</a></li>
        $this->assign('pagestr', $pagestr);
        $this->assign('cond', $cond);
        $this->assign('articles', $art);
        $this->display('articleIndex.html');
    }

    //文章添加方法
    public function add() {
        if (!$_SESSION['categories']) {
            $_SESSION['categories'] = (new \admin\model\CategoryModel())->getAllCategories();
        }
        $this->display('articleAdd.html');
    }
    public function insert() {
        $data = $_POST;
        //验证标题内容是否为空
        if (!trim($data['a_title']) || !trim($data['a_content'])) $this->msg(0, '内容和标题不能为空', 'add', C, P);
        //验证该分类是否存在
        if (!array_key_exists($data['c_id'], $_SESSION['categories'])) $this->msg(0, '该分类不存在', A, C, P);
        //处理图片
        if ($a_img = \vendor\Uploader::uploadOne($_FILES['a_img'], PUBLIC_PATH . 'uploads')) {
            $data['a_img']       = $a_img;
            $data['a_img_thumb'] = \vendor\Image::makeThumb(UPLOAD_PATH . $a_img, PUBLIC_PATH . 'uploads/thumb/');
        }
        //获取其他文章数据
        $data['u_id']     = $_SESSION['user']['id'];
        $data['a_author'] = $_SESSION['user']['u_username'];
        $data['a_time']   = time();
        if (!(new \admin\model\ArticleModel())->autoInsert($data)) {
            if (!$a_img) {
                $this->msg(0, '文章上传成功,图片失败,失败原因' . \vendor\Uploader::$error, 'index');
            }
            if (!$data['a_img_thumb']) {
                $this->msg(0, '博文插入成功,罗略图制作失败,原因' . \vendor\Image::$error, 'index');
            }
            $this->msg(1, '新增文章成功', 'index');
        }
        else {
            //插入数据失败, 文件删除#
            @unlink(PUBLIC_PATH . 'uploads/' . $a_img);
            $this->msg(0, '新增博文失败', 'add');
        }
    }
    //文章删除方法
    public function delete() {
        $id = (int)$_GET['id'];
        if (!(new \admin\model\ArticleModel())->deleteArticle($id)) $this->msg(1, '删除成功', 'index');
        else $this->msg('0', '删除失败', 'index');
    }
    public function edit() {
        if (!isset($_SESSION['categories'])) {
            $_SESSION['categories'] = (new \admin\model\CategoryModel())->getAllCategories();
        }
        $id  = (int)$_GET['id'];
        $art = (new \admin\model\ArticleModel())->getArticleById($id);
        if (!$art) $this->msg('0', '当前要编辑的博文不存在', 'index');
        /*  [id] => 12, [a_title] => 12121, [a_content] => 1212, [c_id] => 4, [u_id] => 1, [a_author] => admin ,[a_time] => 1578019767
        [a_status] => 1, [a_toped] => 2, [a_img] =>, [a_img_thumb] =>, [a_is_delete] => 0, [a_hot] => 0*/
        $this->assign('art', $art);
        $this->display('articleEdit.html');
    }
    public function update() {
        $data              = array();
        $id                = (int)$_REQUEST['id'];
        $data['a_title']   = trim($_REQUEST['a_title']);
        $data['c_id']      = (int)$_REQUEST['c_id'];
        $data['a_status']  = (int)$_REQUEST['a_status'];
        $data['a_content'] = trim($_REQUEST['a_content']);
        $data['a_toped']   = (int)($_REQUEST['a_toped']);
        if (empty($data['a_title']) || empty($data['a_content'])) $this->back('标题和内容不能为空');
        //获取当前文章数据
        $a    = new \admin\model\ArticleModel();
        $art  = $a->getArticleById($id);
        $data = array_diff_assoc($data, $art);
        //判断需不需要更新
        if (empty($data) && $_FILES['a_img']['error'] == 4) $this->back('没有要更新的内容');
        //如果需要  自动更新文章
        //先处理图片
        if ($a_img = \vendor\Uploader::uploadOne($_FILES['a_img'], PUBLIC_PATH . 'uploads')) {
            $data['a_img']       = $a_img;
            $data['a_img_thumb'] = \vendor\Image::makeThumb(UPLOAD_PATH . $a_img, UPLOAD_PATH . 'thumb/');
        }
        if (!$a->autoUpdate($id, $data)) {
            if (!$a_img) $this->msg(0, '没有更新图片,更新好了文章' . \vendor\Uploader::$error, 'index');
            if (!$data['a_img_thumb']) $this->msg('0', '没有更新缩略图,更新了文章');
            $this->msg(1, '更新成功', 'index');
        }
        else {
            @unlink(PUBLIC_PATH . 'uploads/' . $a_img);
            $this->msg(0, '更新失败', 'index');
        }
    }
}

