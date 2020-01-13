<?php
/*
created by syob
*/
namespace admin\model;
use core\Model;
class ArticleModel extends Model {
    protected $table = 'article';
    /*
     * 获取文章信息
     * @param1 array $cond      查询条件
     * @param2 int   $pagecount 每页的文章数
     * @param3 array $page      起始页面
     * return 查询结果的数组
     */
    public function getArticleInfo(array $cond = array(), int $pagecount = 5, int $page = 1) {
        //设置文章没有删除的, 展现出来
        $where = " where a_is_delete = 0";
        //组装各个条件
        foreach ($cond as $k => $v) {
            switch ($k) {
                case 'a_title':
                    $where .= " and a_title like '{%$v%}'";
                case 'c_id':
                case 'a_status':
                case 'a_toped':
                case 'u_id':
                    $where .= " and {$k} = {$v}";
                    break;
            }
        }
        //设置偏移量
        $offset = ($page - 1) * $pagecount;
        $sql = "select a.id,a.a_title,a.a_author,a.a_time,a.a_status,c.c_name
                    from {$this->getTable()} a
                    left join {$this->getTable('category')} c
                    on a.c_id = c.id {$where}
                    order by a_time desc 
                    limit {$offset},{$pagecount}";
        return $this->query($sql, false);
    }
    public function deleteArticle($id) {
        $sql = "update {$this->getTable()} set a_is_delete = 1  where id = {$id}";
        return $this->exec($sql);
    }
    //根据id获取文章所有内容
    public function getArticleById($id) {
        $sql = "select * from {$this->getTable()} where id = {$id}";
        return $this->query($sql);
    }
    public function getSearchCounts($cond = array()) {
        $where = ' where a_is_delete = 0 ';
        foreach ($cond as $k => $v) {
            switch ($k) {
                case 'a_title':
                    $where .= " and a_title like '%{$v}%'";
                    break;
                case 'c_id':
                case 'a_status':
                case 'a_toped':
                case 'u_id':
                    $where .= " and {$k} = {$v} ";
                    break;
            }
        }
        $sql = "select count(*) c from {$this->getTable()} {$where}";
        $res = $this->query($sql);
        return $res['c'] ?? 0;
    }
    //验证该分类下是否有文章
    public function checkArticleByCategory(int $c_id){
        $sql = "select id from {$this->getTable()} where c_id = '{$c_id}'";
        return $this->query($sql,1);
    }

}