<?php
/*
created by syob
*/
namespace admin\model;
use core\Model;
class CategoryModel extends Model {
    protected $table = 'category';

    //获取所有分类数组,这时候没有分级

    //查找同一夫分类($c_parent_id)下, 该c_name的id, 能查到就是重名了
    public function checkCategoryName(int $c_parent_id, string $c_name) {
        $sql = "select id from {$this->getTable()} where c_parent_id = {$c_parent_id} and c_name = '{$c_name}'";
        return $this->query($sql);
    }

    public function insertCategory($c_name, int $c_sort, int $c_parent_id) {
        $sql = "insert into {$this->getTable()} values(null,'{$c_name}',{$c_sort},{$c_parent_id})";
        return $this->exec($sql);
    }
    //查看是否又子分类
    public function getSon(int $id) {
        $sql = "select id from {$this->getTable()} where c_parent_id = {$id}";
        return $this->query($sql);
    }

}