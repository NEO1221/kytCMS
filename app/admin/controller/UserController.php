<?php
/*
created by syob
*/
namespace admin\controller;
use core\Controller;
class UserController extends Controller {
    public function add() {
        $this->display('userAdd.html');
    }
    public function index() {
        $page       = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $page_count = 10;
        $url        = URL . 'index.php';
        $cond       = array('p' => 'admin', 'c' => 'user', 'a' => 'index');
        $user_num   = (int)((new \admin\model\UserModel())->getAllUserNum());

        //获取分页, 用户数据
        $user_data  = (new \admin\model\UserModel())->getAllUserPage($page_count, $page);

        $page_str   = \vendor\Page::clickPage($url, $user_num, $page_count, $page, $cond);
        if (!$user_data) $this->msg('0', '用户查询失败', 'index');
        $this->assign('pagestr', $page_str);
        $this->assign('user_data', $user_data);
        $this->display('userIndex.html');
    }
    public function insert() {
        $data = $_POST;
        //        $data['u_username'] = trim($_POST['u_username']);
        //        $data['u_password'] = trim($_POST['u_password']);
        //        $data['u_is_admin'] = (int)$_POST['u_is_admin'];
        if (empty(trim($data['u_username'])) || empty(trim($data['u_password']))) $this->msg(0, '姓名和密码不能为空', 'index');
        //检验用户名有效性,是否有重复的
        if ((new \admin\model\UserModel())->checkUsername(trim($data['u_username']))) $this->msg('0', "用户名" . $data['u_username'] . "重复了", 'index');
        $data['u_reg_time'] = time();
        $data['u_password'] = md5($data['u_password']);
        $res                = (new \admin\model\UserModel())->autoInsert($data);
        if (!$res) $this->msg(0, '成功加入管理员', 'index');
        else $this->msg('1', '管理员添加失败', 'index');
    }
    public function edit() {
        $id        = $_GET['id'];
        $user_data = (new \admin\model\UserModel())->getById($id);
        if (!$user_data) $this->msg(0, '没有此用户', 'index');
        $_SESSION['user_edit'] = $user_data;
        $this->assign('user_data', $user_data);
        $this->display('userEdit.html');
    }
    public function delete() {
        $id = $_GET['id'];
        if ($id == $_SESSION['user']['id']) $this->msg('0', '不能删除自己', 'index');
        if ((new \admin\model\UserModel())->deleteById($id)) {
            $this->msg('0', '删除失败', 'index');
        }
        else $this->msg('1', '删除成功', 'index');
    }
    public function update() {
        $id                 = (int)$_GET['id'];
        $data               = array();
        $data['u_username'] = trim($_POST['u_username']);
        $data['u_is_admin'] = (int)$_POST['u_is_admin'];
        if (trim($_POST['u_password']) != '') $data['u_password'] = md5(trim($_POST['u_password']));
        if ($data['u_username'] == $_SESSION['user_edit']['u_username'] && $data['u_is_admin'] == $_SESSION['user_edit']['u_is_admin'] && trim($_POST['u_password']) == '') {
            $this->back('没有更新任何内容');
        }
        if (!(new \admin\model\UserModel())->autoUpdate($id, $data)) $this->msg('1', '更新成功', 'index');
        else $this->msg('0', '更新失败', 'index');
    }

}