<?php
/*
created by syob
*/
namespace vendor;
class Uploader {
    //    protected static $config = CONFIG_PATH . 'config.php';
    //允许上传文件的类型
    private static $types = array('image/jpg', 'image/jpeg', 'image/pjpeg');
    //定义外部可以获取的错误信息
    public static $error;
    //用外部方法设定可以传入的类型
    public static function setTypes(array $types) {
        if (!empty($types)) $types = self::$types;
    }
    /*
     * @param1  array $file 要上传单个的文件信息
     * @param2  sting $path  要上传的文件路径
     * @param3  int $max=2000000   限定大小,默认为2M
     * @return  mixed ,成功返回文件名字, 失败返回false
     * */
    public static function uploadOne(array $file, string $path, int $max = 2000000) {
        //判定有无错误信息,并且数组是否为5
        if (!isset($file['error']) || count($file) != 5) {
            self::$error = '无效的文件';
            return false;
        }
        //判定储存路径是否存在
        if (!is_dir($path)) {
            self::$error = '文件路径不存在';
            return false;
        }
        switch ($file['error']) {
            case 1:
            case 2:
                self::$error = '文件超过服务器允许大小';
                return false;
            case 3:
                self::$error = '文件只有部分被上传';
                return false;
            case 4:
                self::$error = '没有选中要上传的文件';
                return false;
            case 6:
            case 7:
                self::$error = '服务器错误';
                return false;
        }
        if (!in_array($file['type'], self::$types)) {
            self::$error = '当前文件类型不允许上传';
            return false;
        }
        if ($file['size'] > $max) {
            self::$error = '当前文件超过允许大小';
            return false;
        }
        //此时,文件没有问题, 获取文件的新名字
        $filename = self::getRandomName($file['name']);
        //移动文件
        if (move_uploaded_file($file['tmp_name'], $path . '/' . $filename)) {
            return $filename;
        }        else {
            self::$error = '文件移动失败';
            return false;
        }
    }
    public static function getRandomName(string $filename, $prefix = 'image') {
        //获取文件后缀名
        //        $temp = explode(".", $_FILES["file"]["name"]);
        //        $extension = end($temp);
        //          下面的不太好
        $ext     = strchr($filename, '.');

        //$ext = end(explode('.', $filename));
        $newname = $prefix . date('YmdHis');
        for ($i = 0; $i < 6; $i++) {
            $newname .= chr(mt_rand(65, 90));
        }
        return $newname . $ext;
    }
}