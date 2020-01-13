<?php
/*
created by syob
*/
namespace home\model;

use core\Model;
class ArticleModel extends Model {
    protected $table = 'article';
    public function getAllArticles(array $cond = array(), int $page_count = 5, int $page = 1){
        $where = " where a_is_delete = 0 ";
        foreach($cond as $k=>$v){
            switch($k){
                case 'a_title':
                    $where .= " and a_title like '%{$v}%' ";
                    break;
                case 'c_id':
                    $where .= " and $k = '{$v}'";
                    break;
            }
        }
        $offset = ($page-1) * $page_count;

        $sql = "select a.id,a.a_title,a.a_author,a.a_time,c.c_name,ct.c_count
        from {$this->getTable()} a
        left join {$this->getTable('category')} c
        on a.c_id = c.id
        left join (select a_id,count(*) c_count from {$this->getTable('comment')} group by a_id) ct
        on a.id = ct.a_id
        {$where}
        order by a.id desc
        limit {$offset}, {$page_count}";

        return $this->query($sql,0);
    }
    public function getCountsByCategory(){
        $sql = "select c_id,count(*) num from {$this->getTable()} where a_is_delete = 0 group by c_id";
        $res = $this->query($sql,0);
        $list = array();
        foreach($res as $value){
            $list[$value['c_id']] = $value['num'];
        }
        return $list;
    }
    public function getNewsInfo(int $limit = 3){
        $sql = "select id,a_title,a_img_thumb from {$this->getTable()} order by id desc limit {$limit}";
        return $this->query($sql,0);
    }
    //获取该条件下的所有数据
    public function getCounts(array $cond= array()){
        $where = "where a_is_delete = 0 ";
        foreach($cond as $k=>$v){
            switch($k){
                case 'a_title':
                    $where .=  " and a_title like '%{$v}%'" ;
                    break;
                case 'c_id':
                    $where .=  " and {$k} = {$v}" ;
                    break;
            }
        }
        $sql = "select count(*) c from {$this->getTable()} {$where}";
        $res = $this->query($sql);
        return $res['c'] ?? 0;
    }
}



