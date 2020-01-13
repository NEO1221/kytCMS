<?php
/*
created by syob
*/
namespace core;
class Controller {
    protected $smarty;

    public function __construct() {
        include VENDOR_PATH . 'smarty/Smarty.class.php';
        $this->smarty                 = new \Smarty();
        $this->smarty->template_dir   = APP_PATH . P . '/view/' . C . '/';
        $this->smarty->caching        = false;
        $this->smarty->cache_dir      = APP_PATH . P . '/cache';
        $this->smarty->cache_lifetime = 120;
        $this->smarty->compile_dir    = ROOT_PATH . 'template_c';
        if (P == "admin") {
            @session_start();
            if (!isset($_SESSION['user']) && strtolower(C) !== 'privilege') {
                if (isset($_COOKIE['user_id'])) {
                    $u    = new \admin\model\UserModel();
                    $user = $u->getById((int)$_COOKIE['user_id']);
                    if ($user) {
                        $_SESSION['user'] = $user;
                        $this->msg(1, '七天免登录', 'index', 'Index');
                    }
                }
                $this->msg(0, '请先登录', 'index', 'Privilege');
            }
        }
    }

    //重写smarty方法
    protected function assign($key, $value) {
        $this->smarty->assign($key, $value);
    }
    protected function display($file) {
        $this->smarty->display($file);
    }
    //自定义信息模板
    protected function msg($success = 1, $msg, $a = A, $c = C, $p = P, $time = '') {
        //refresh->Refresh:3;url = www.mvc.com/index.php?p=home&c=Index&a=index
        $config = include CONFIG_PATH . 'config.php';
        if ($time == '') $time = (int)$config['setting']['jump_time'];
        $refresh = 'Refresh:' . $time . ';url=' . URL . 'index.php?p=' . $p . '&c=' . $c . '&a=' . $a;
        header($refresh);
        echo $msg;
        exit;
    }
    protected function back(string $msg, $time = '') {
        $config = include CONFIG_PATH . 'config.php';
        if ($time == '') $time = (int)$config['setting']['jump_time'];
        header("Refresh:{$time};url={$_SERVER['HTTP_REFERER']}");
        echo $msg; exit;
    }
}

