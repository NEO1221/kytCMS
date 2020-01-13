<?php
/*
created by syob
*/
namespace home\model;
use core\Model;
class FlinkModel extends Model {
    protected $table = 'link';
    public function getAll() {
        $sql = "select * from {$this->getTable()} where f_is_delete = 0";
        return $this->query($sql, 0);
    }
}