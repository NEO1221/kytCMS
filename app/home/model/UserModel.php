<?php
/*
created by syob
*/
namespace home\model;
use core\Model;
class UserModel extends Model {
    protected $table = 'user';
    public function checkUserName(string $username) {
        $sql = "select * from {$this->getTable()} where u_username = '{$username}'";
        return $this->query($sql,1);
    }
}