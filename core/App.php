<?php
/*
created by syob
*/


namespace core;

if (!defined("ACCESS")) {
    header("location:../public/index.php");
    exit;
}

class App {
    public static function start() {
        self::set_path();
        self::set_error();
        self::set_config();
        self::set_url();
        self::set_autoload();
        self::set_dispatch();
    }

    private static function set_path() {
        define('CORE_PATH', ROOT_PATH . 'core/');
        define('PUBLIC_PATH', ROOT_PATH . 'public/');
        define('APP_PATH', ROOT_PATH . 'app/');
        define('ADMIN_PATH', APP_PATH . 'admin/');
        define('ADMIN_VIEW', ADMIN_PATH . 'view/');
        define('HOME_PATH', APP_PATH . 'home/');
        define('VENDOR_PATH', ROOT_PATH . 'vendor/');
        define('ADMIN_CONT', ADMIN_PATH . 'controller/');
        define('ADMIN_MODEL', ADMIN_PATH . 'model/');
        define('HOME_CONT', HOME_PATH . 'controller/');
        define('HOME_MODEL', HOME_PATH . 'model/');
        define('CONFIG_PATH', ROOT_PATH . 'config/');
        define('UPLOAD_PATH', PUBLIC_PATH . 'uploads/');
        define('URL', 'http://blog.com/');

    }

    private static function set_error() {
        @ini_set('error_reporting', E_ALL);
        @ini_set('display_errors', 1);
    }

    private static function set_config() {
        global  $config;
        $config = include CONFIG_PATH . 'config.php';

    }

    private static function set_url() {
        global $config;
        $p = $_REQUEST['p'] ?? $config['setting']['P'];
        $c = $_REQUEST['c'] ?? $config['setting']['C'];
        $a = $_REQUEST['a'] ?? $config['setting']['A'];

        define('P', $p);
        define('C', $c);
        define('A', $a);
    }

    private static function set_autoload_function($class) {
        $class = basename($class);
        //加载核心文件
        $path  = CORE_PATH . $class . '.php';
        if (file_exists($path)) {
            include $path;
        }
        $path = APP_PATH . P . '\\controller\\'.$class .'.php';
        if(file_exists($path)) include $path;
        $path = APP_PATH . P . '\\model\\' .$class .'.php';
        if(file_exists($path)) include $path;
//        if (P == 'home') {
//            $path = HOME_CONT . $class . '.php';
//            if (file_exists($path)) {
//                include $path;
//            }
//            $path = HOME_MODEL . $class . '.php';
//            if (file_exists($path)) {
//                include $path;
//            }
//        } else {
//            $path = ADMIN_PATH . $class . '.php';
//            if (file_exists($path)) {
//                include $path;
//            }
//            $path = ADMIN_MODEL . $class . '.php';
//            if (file_exists($path)) {
//                include $path;
//            }
//        }
        $path = VENDOR_PATH . $class . '.php';
        if (file_exists($path)) {
            include $path;
        }
    }


    private static function set_autoload() {
        spl_autoload_register(array(__CLASS__, 'set_autoload_function'));
    }
    private static function set_dispatch() {
        $p = P;
        $c = C;
        $a = A;
        $c .= 'Controller';
        $spacename = "\\$p\\controller\\$c";
        $obj       = new $spacename();
        $obj->$a();

//        new \home\controller\IndexController();
    }
}