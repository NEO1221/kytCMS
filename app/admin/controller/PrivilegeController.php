<?php
/*
created by syob
*/

namespace admin\controller;

use \core\Controller;
use \vendor\Captcha;

class PrivilegeController extends Controller {
    public function index() {
        $this->display("login.html");
    }

    public function check() {
        $username = $_POST['u_username'] ?? '';
        $password = $_POST['u_password'] ?? '';
        $captcha  = $_POST['captcha'] ?? '';
        if(empty($captcha)){
            $this->msg(1, '验证码不能为空','index');
        }
        if(!\vendor\Captcha::checkCaptcha($captcha)){
            $this->msg(1,'验证码错误','index');
        }
        if (empty(trim($username)) || empty(trim($password))) {
            $this->msg(0, '账号密码不能为空', 'index');
        }
        $user = new \admin\Model\UserModel();
        $user = $user->getUserByUsername($username);

        if (!$user) {
            $this->msg(0, '提示信息:' . $username . '不存在', 'index');
        }
        if ($user['u_password'] != md5($password)) {
            $this->msg(0, '提示信息用户密码错误', 'index');
        }
        //此时登录成功
        $_SESSION['user'] = $user;
        if (isset($_POST['rememberMe'])) {
            setcookie('user_id', $user['id'], time() + 7 * 30 * 3600);
        }
        $this->msg(1, '登录成功,1s后进入后台.', 'index', 'index');
    }

    public function logout() {
        setcookie('user_id', '', 1);
        session_destroy();
        $this->msg('1', '感谢登录,欢迎回来', 'index', 'index', 'home');
    }

    public function captcha() {
        \vendor\Captcha::getCaptcha();
    }

}

