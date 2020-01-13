<?php
/*
created by syob
*/
namespace admin\controller;
use admin\model\CategoryModel;
use core\Controller;
class CategoryController extends Controller {
    public function index() {
        $_SESSION['categories'] = (new \admin\model\CategoryModel())->getAllCategories();
        $this->display('categoryIndex.html');
    }

    public function add() {
        if(!$_SESSION['categories']){
            $_SESSION['categories'] = (new \admin\model\CategoryModel)->getAllCategories();
        }
        $this->display('categoryAdd.html');
    }

    public function insert() {
        $c_name      = $_POST['c_name'];
        $c_sort      = $_POST['c_sort'];
        $c_parent_id = $_POST['c_parent_id'];
        if (empty($c_name)) {
            $this->msg(0, '名称不能为空', 'add');
        }
        if (!is_numeric($c_sort) || (int)$c_sort != $c_sort || $c_sort < 0 || $c_sort > PHP_INT_MAX) {
            $this->msg(0, '排序必须为正整数', 'add');
        }
        $c = new \admin\Model\CategoryModel();
        if ($c->checkCategoryName((int)$c_parent_id, $c_name)) {
            $this->msg(0, '所选分类下已经有该分类', 'add');
        }
        if (!$c->insertCategory($c_name, (int)$c_sort, (int)$c_parent_id)) {
            $this->msg(1, '成功新增数据', 'add');
        }
        else $this->msg(0, '新增数据失败', 'add');
    }
    public function delete() {
        $id = (int)$_GET['id'];
        $c  = new \admin\Model\CategoryModel();
        //验证该栏目下是否又分类
        if ($c->getSon($id)) $this->msg(0, '该分类下有id,不能删除', 'index');
        //验证该栏目下有没有文章
        if((new \admin\model\ArticleModel())->checkArticleByCategory($id)) $this->msg(0,'该文类下有文章不能删除','index');
        //删除分类
        if (!$c->deleteById($id)) $this->msg(1, '删除分类--成功', 'index');
        else $this->msg(0, '删除分类--失败', 'index');
    }
    //编辑分类
    public function edit() {
        $id = (int)$_GET['id'];
        //验证当前要编辑的分类是否存在
        if (!array_key_exists($id, $_SESSION['categories'])) $this->msg(0, '当前分类不存在', 'index');
        //重新获取一遍分类
        $c = new \admin\model\CategoryModel();
        $categories = $c->noLimitCategory($_SESSION['categories'],0,0,$id);
        $this->assign('categories',$categories);
        $this->assign('id', $id);
        $this->display('categoryEdit.html');
    }
    //更新分类
    public function update() {
        //合法性验证
        $id                  = (int)$_POST['id'];   //后台提交,无需验证
        $data['c_name']      = trim($_POST['c_name']);
        $data['c_sort']      = trim($_POST['c_sort']);
        $data['c_parent_id'] = (int)$_POST['c_parent_id']; //固定数据,无需验证
        if (empty($data['c_name'])) $this->msg(0, '分类名称不能为空', 'index');
        if (!is_numeric($data['c_sort']) || (int)$data['c_sort'] != $data['c_sort'] || $data['c_sort'] < 0 || $data['c_sort'] > PHP_INT_MAX) $this->msg(0, '排序必须是正整数', 'back');

        //数据有效性验证
        $c   = new \admin\model\CategoryModel();
        $cat = $c->checkCategoryName((int)$data['c_parent_id'], $data['c_name']);
        if ($cat && $cat['id'] != $id) $this->msg(0, '该父栏目下存在次分类', 'index');

        //通过session判断,是否有数据更新
        $data = array_diff_assoc($data, $_SESSION['categories'][$id]);
        if (empty($data)) $this->msg(0, '数据没有任何更新', 'index');

        //更新数据
        if (!$c->autoUpdate($id, $data)) {
            $_SESSION['categories'] = $c->getAllCategories();
            $this->msg(0, '更新成功', 'index');
        }else $this->msg(1, '更新失败', 'index');
        //更新session

    }
}