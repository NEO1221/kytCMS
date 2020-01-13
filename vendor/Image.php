<?php
/*
created by syob
*/
namespace vendor;
class Image {
    //设置可以gd库后缀,可以操作的gd库
    private static $ext = array('jpg' => 'jpeg', 'jpeg' => 'jpeg', 'png' => 'png', 'gif' => 'gif');
    //设置错误信息
    public static $error;
    /*
     * 制作做略图
     * @param1 $file 文件信息
     * @param2 $path 处理后的存储路径
     * @param3 $width 缩略图的宽度
     * @param4 $height 缩略图的高度
     * return 返回缩略图的名字
     * */
    public static function makeThumb($file, $path, $width = 90, $height = 90) {
        if (!file_exists($file)) {
            self::$error = '原图文件不存在';
            return false;
        }
        if (!is_dir($path)) {
            self::$error = '存储路径不存在';
            return false;
        }
        /* pathinfo函数
         * return $data['dirname'] = /testweb;
         * return $data['basename'] = test.jpg;
         * return $data['extension'] = jpg;
         * 存入$file_info数组
         * */
        $file_info = pathinfo($file);
        /*
         * 返回图像文件的高度宽度getimagesize();
            $image_info  array(7) {
            [0] =>int(194) 给出的是图像宽度的像素值
            [1] =>int(194) 给出的是图像高度的像素值
            [2] =>int(2)    给出的是图像的类型, 1 = GIF，2 = JPG，3 = PNG
            [3] =>string(24) "width="194" height="194""  可以直接用于 HTML 的 <image> 标签
            'bits' =>int(8)  给出的是图像的每种颜色的位数
            'channels' =>int(3) 给出的是图像的通道值，RGB 图像默认是 3
            'mime' =>string(10) "image/jpeg"  给出的是图像的 MIME 信息
} * */
        $img_info = getimagesize($file);
        var_dump($img_info);
        //判定key,是否在array里面
        if (!array_key_exists($file_info['extension'], self::$ext)) {
            self::$error = '当前文件类型不能制作缩略图';
            return false;
        }
        //构造gd函数打开和关闭方法, $open = imagecreatefromjpeg; $close = imagejpeg;
        $open  = 'imagecreatefrom' . self::$ext[$file_info['extension']];
        $close = 'image' . self::$ext[$file_info['extension']];
        //打开原图资源
        $src = $open($file);
        //创建一个自定义大小的画布
        $thumb = imagecreatetruecolor($width, $height);
        //再次画布分配颜色
        $bg = imagecolorallocate($thumb, 255, 255, 255);
        //填充背景色
        imagefill($thumb, 0, 0, $bg);
        //计算宽高,
        //$src_b  原图的宽度除以图像的高度
        $src_b   = $img_info[0] / $img_info[1];
        //$thumb_b 缩略图的宽度除以高度;
        $thumb_b = $width / $height;
        //原图的横向更长, 宽高比
        if ($src_b > $thumb_b) {
            //把最终缩略图的宽, 设置成缩略图画布的宽
            $w = $width;
            //图像的高度就是, 高度除以宽高比
            $h = ceil($width / $src_b);
            $x = 0;
            $y = ceil($height - $h) / 2;
        }
        else {
            //原图横向更方,需要压缩宽度,高度不变
            $w = ceil($src_b * $height);
            $h = $height;
            $x = ceil(($width - $w) / 2);
            $y = 0;
        }
        /*
            imagecopyresampled() 将一幅图像中的一块正方形区域拷贝到另一个图像中
            dst_image     目标图象连接资源。 $thumb缩略图
            src_image     源图象连接资源。  $src 原图资源
            dst_x         目标 X 坐标点。     $x
            dst_y         目标 Y 坐标点。     $y
            src_x         源的 X 坐标点。     0
            src_y         源的 Y 坐标点。     0
            dst_w         目标宽度。         $w
            dst_h         目标高度。         $h
            src_w         源图象的宽度。
            src_h         源图象的高度。
        */
        if (!imagecopyresampled($thumb, $src, $x, $y, 0, 0, $w, $h, $img_info[0], $img_info[1])) {
            self::$error = '缩略图生成时,制作失败';
            return false;
        }
        // imagejpeg() 从 image 图像以 filename 为文件名创建一个 JPEG 图像。
        $res = $close($thumb, $path . 'thumb_' . $file_info['basename']);
        imagedestroy($src);
        imagedestroy($thumb);
        if ($res) {
            return 'thumb_' . $file_info['basename'];
        }
        else {
            self::$error = '缩略图保存失败';
            return false;
        }
    }
}
