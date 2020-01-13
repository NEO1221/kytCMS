<?php
/*
created by syob
*/

namespace admin\model;

use core\Model;

class UserModel extends Model {
    protected $table = 'user';

    public function getUserByUsername($username) {
        $username = addslashes($username);
        $sql      = "select * from {$this->getTable()} where u_username = '{$username}'";
        return $this->query($sql);
    }

    public function getCounts() {
        $sql = "select count(*) as c from {$this->getTable()}";
        $res = $this->query($sql);
        return $res['c'] ?? 0;
    }





/*
 * 此方法和model中的autoInsert重复
 *     public function addUser(array $data){
        $in_fields = $in_values = '(';
        foreach($data as $k=>$v){
            $in_fields .= "{$k},";
            $in_values .= "'{$v}',";
        }
        $in_fields = rtrim($in_fields,',').')';
        $in_values = rtrim($in_values,',').')';
        $sql = "insert into {$this->getTable()} {$in_fields} value {$in_values}";
        return $this->exec($sql);
    }*/

    public function getAllUserNum(){
        $sql = "select count(*) c from {$this->getTable()}";
        $res = $this->query($sql);
        return $res['c'] ?? 0;
    }
    public function getAllUserPage(int $page_count , int $page){
        $limit = ($page-1) * $page_count;
        $sql = "select * from {$this->getTable()} order by id desc limit {$limit}, {$page_count} ";
        return $this->query($sql,0);
    }
    public function checkUserName(string $username){
        $sql = "select id from {$this->getTable()} where u_username = '{$username}'";
        return $this->query($sql);
    }
}