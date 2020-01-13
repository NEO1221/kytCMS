<?php
/*
created by syob
*/
namespace admin\model;
use core\Model;
class CommentModel extends Model {
    protected $table = 'comment';
    //留言分页处理
    public function getAllComments(int $page_count = 5, int $page = 1) {
        $offset = ($page - 1) * $page_count;
        $sql    =   "select c.*,u.u_username,a.a_title from
                    {$this->getTable()} c
                    left join
                    {$this->getTable('article')} a on c.a_id = a.id
                    left join
                    {$this->getTable('user')} u on c.u_id = u.id
                    order by c.c_time desc,c.a_id desc
                    limit {$offset},{$page_count}";
        return $this->query($sql, 0);
    }
}