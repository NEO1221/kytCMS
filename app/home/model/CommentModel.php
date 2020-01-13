<?php
/*
created by syob
*/
namespace home\model;
use core\Model;
class CommentModel extends Model {
    protected $table = 'comment';
    public function getCommentsByArticle(int $a_id) {
        $sql = "select c.*,u.u_username
                from {$this->getTable()} c
                left join {$this->getTable('user')} u
                on c.u_id = u.id
                where c.a_id = {$a_id}
                order by c.c_time";
        return $this->query($sql, 0);
    }

}