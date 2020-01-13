<?php
/*
created by syob
*/

namespace vendor;
class Captcha {
    public static function getCaptcha($width = 450, $height = 85, $length = 4, $fonts = '') {
        //设置字体
        if (empty($fonts))
            $fonts = 'verdana.ttf';
        $fonts = __DIR__ . '/fonts/' . $fonts;

        //设置画布
        $img      = imagecreatetruecolor($width, $height);
        $bg_color = imagecolorallocate($img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        imagefill($img, 0, 0, $bg_color);
        //增加干扰点
        for ($i = 0; $i < 50; $i++) {
            $dots_color = imagecolorallocate($img, mt_rand(149, 190), mt_rand(140, 190), mt_rand(140, 190));
            imagestring($img, mt_rand(1, 5), mt_rand(0, $width), mt_rand(0, $height), '*', $dots_color);
        }
        //增加干扰线
        for ($i = 0; $i < 10; $i++) {
            $line_color = imagecolorallocate($img, mt_rand(80, 130), mt_rand(80, 130), mt_rand(80, 130));
            imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $line_color);
        }
        //获取缓存字符,并且写入到session中
        $captcha = self::getString($length);
        @session_start();
        $_SESSION['captcha'] = $captcha;
        //写入到验证码里
        for ($i = 0; $i < $length; $i++) {
            $c_color = imagecolorallocate($img, mt_rand(0, 60), mt_rand(0, 60), mt_rand(0, 60));
            imagettftext($img, mt_rand(20, 25), mt_rand(-45, 45), $width / ($length + 1) * ($i + 1), mt_rand(25, $height - 25), $c_color, $fonts, $captcha[$i]);
        }

        //输出图片并销毁
        header("Content-type:image/png");
        imagepng($img);

        imagedestroy($img);

    }

    public static function checkCaptcha($captcha){
        @session_start();
        return (strtolower($_SESSION['captcha']) ==  strtolower($captcha));
    }


    private static function getString($length = 4) {
        $captcha = '';
        for ($i = 0; $i < $length; $i++) {
            switch (mt_rand(1, 3)) {
                case 1:
                    $captcha .= chr(mt_rand(49, 57));
                    break;
                case 2:
                    $captcha .= chr(mt_rand(65, 90));
                    break;
                case 3:
                    $captcha .= chr(mt_rand(97, 122));
                    break;
            }
        }
        return $captcha;
    }
}