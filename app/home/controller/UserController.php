<?php
/*
created by syob
*/
namespace home\controller;
use core\Controller;
class UserController extends Controller {
    public function check() {
        //后台接受传入的数据
        $u_username = trim($_REQUEST['username']);
        $u_password = trim($_REQUEST['password']);
        //验证数据不为空
        if (empty($u_username) || empty($u_password)) $this->msg(0, '用户名或者密码不为空', 'index', 'Index');
        //验证用户名
        $user = (new \home\model\UserModel())->checkUserName($u_username);
        if (!$user) $this->msg(0, '登录失败,用户名或密码错误或者不存在', 'index', 'Index');
        //验证密码
        if (md5($u_password) != $user['u_password']) $this->msg(0, '登录失败,用户名或密码错误或者不存在', 'index', 'Index');

        @session_start();
        $_SESSION['user'] = $user;
        $this->msg(1, '登录成功', 'index', 'Index');
    }
    //用户退出登录
    public function logout() {
        @session_start();
        session_destroy();
        $this->msg(1, '退出登录', 'index', 'Index');
    }
    //用户注册
    public function register() {
        //接受数据
        $data               = array();
        $data['u_username'] = trim($_POST['u_username']);
        $data['u_password'] = trim($_POST['u_password']);
        $captcha            = trim($_POST['captcha']);
        //验证合法性
        if (empty($data['captcha'])) $this->msg(1, '验证码不能为空');
        if (empty($data['u_username']) || empty($data['u_password'])) $this->msg(0, '用户名和密码不能为空');
        if (!\vendor\Captcha::checkCaptcha($captcha)) $this->msg('1', '验证码不正确');
        $u = new \home\model\UserModel();
        if ($u->checkUserName($data['u_username'])) $this->msg(0, '用户名存在');
        //补充入库数据
        $data['u_reg_time'] = time();
        if ($u->autoInsert($data)) $this->msg(1,'注册成功');
        else $this->msg(0, '注册失败');
    }
    //生成验证码
    public function captcha() {
        \vendor\Captcha::getCaptcha();
    }
}