<?php
/*
created by syob
*/
namespace core;
use \PDO;
use \PDOStatement;
use \PDOException;
class Dao {
    private $pdo;

    public function __construct($dbinfo = array()) {
        $type    = $dbinfo['type'] ?? 'mysql';
        $host    = $dbinfo['host'] ?? 'localhost';
        $port    = $dbinfo['port'] ?? '3306';
        $user    = $dbinfo['user'] ?? 'root';
        $pass    = $dbinfo['pass'] ?? 'zhangbo123';
        $dbname  = $dbinfo['dbname'] ?? 'test';
        $charset = $dbinfo['charset'] ?? 'utf8';
        //mysql:host=localhost;port=3306;dbname=test,root,root,$drivers
        try {
            $this->pdo = new PDO($type . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname, $user, $pass);
            //            $this->pdo = @new PDO($type . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname, $user, $pass, $drivers);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->dao_exception($e);
        }
        //        $this->dao_charset($charset);
    }

    public function dao_exec($sql) {
        try {
            $this->pdo->exec($sql);
            $this->dao_sql_display($sql);
        } catch (PDOException $e) {
            $this->dao_exception($e, $sql);
        }
    }

    public function dao_insert_id() {
        return $this->pdo->lastInsertId();
    }

    public function dao_query($sql, $only = true) {
        try {
            $stmt = $this->pdo->query($sql);
            $this->dao_sql_display($sql);
            //            打印
            //            var_dump($stmt);
            if ($only) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $this->dao_exception($e, $sql);
        }
    }

    private function dao_charset($charset = 'utf8') {
        try {
            $this->pdo->exec("set names {$charset}");
        } catch (PDOException $e) {
            $this->dao_exception($e);
        }
    }

    //封装错误信息
    protected function dao_exception($e, $sql = "") {
        echo '链接错误!' . '<br/>';
        echo '错误文件为' . $e->getFile() . '<br/>';
        echo '错误行号为' . $e->getLine() . '<br/>';
        echo '错误描述为' . $e->getMessage() . '<br/>';
        echo '错误的sql: ' . $sql;
        exit;
    }
    protected function dao_sql_display($sql) {
        global $config;
        if ($config['setting']['sql_display'] == 1) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sql.'<br/>';
    }

}