<?php
/*
created by syob
线上模式
*/
define('ACCESS', true);
define('ROOT_PATH', __DIR__ . '/../');
require(ROOT_PATH.'core/App.php');
\core\App::start();


/*
 * 数据库测试模式
 **/
//define('ACCESS', true);
//define('ROOT_PATH', __DIR__ . '/../');
//require(ROOT_PATH.'core/Dao.php');


