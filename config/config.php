<?php
/*
created by syob
u_password in D:\www\syobcms\app\home\controller\UserController.php on line 18
*/
return array(
    //数据库配置
    'database' => array(
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'zhangbo123',
        'charset' => 'utf-8',
        'dbname' => 'blog',
        'prefix' => 'b_',

    ),
    //其他配置, 跳转时间
    'setting'=>array(
        'P' => 'home',
        'C' => 'Index',
        'A' => 'Index',
        'jump_time'=>1,
        'sql_display'=>0,
    ),
    'upload_types'=>array(
        'image/jpg','image/jpeg','image/pjpeg',
    ),
    //分页设置, 每页显示的数据
    'admin'=>array(
        'article_page_count'=> 6,
        'comment_page_count'=>5,
    ),
    'home'=>array(
        'article_page_count'=> 5,
    ),


);

/*
 * 数据类型
*******************************************************************************
$_FILES数据
array(1) {
    'a_img'     =>    array(5) {
         'name'     =>    string(10) "1 (22).jpg"
        'type'      =>    string(10) "image/jpeg"
        'tmp_name'  =>    string(50) "D:\phpstudy\Extensions\php\php7.3.4nts\php7AB8.tmp"
        'error'     =>    int(0)
        'size'      =>    int(7892)
      }
}
*******************************************************************************
 * desc user的数据结构
 * *******************************************
 * foreach时
 * key定位到了 [0],[1]
 * value定位到了里面的数组array(6)
 * value['Field'] 就是需要的字段
 * value['Key'] 可以找到是否是主键
 * ********************************************
 * array(4) {
    [0] =>
          array(6) {
             'Field' =>      string(2) "id"
            'Type' =>        string(7) "int(10)"
            'Null' =>        string(2) "NO"
            'Key' =>         string(3) "PRI"
            'Default' =>     NULL
            'Extra' =>       string(14) "auto_increment"
  }
}
************************************************
 * $_SESSION['categories']数组
 * array(7) {
  [1] =>
  array(5) {
    'id' =>    string(1) "1"
    'c_name' =>    string(8) "IT技术"
    'c_sort' =>    string(4) "2223"
    'c_parent_id' =>    string(1) "0"
    'level' =>    int(0)
  }
}
************************************************
$_SESSION['user']数组
array(5) {
  'id' =>  string(1) "1"
  'u_username' =>  string(5) "admin"
  'u_password' =>  string(32) "21232f297a57a5a743894a0e4a801fc3"
  'u_reg_time' =>  string(10) "1577669412"
  'u_is_admin' =>  string(1) "1"
}
***********************************************************
 * $res是一个三维数组
 * array(1) {
  [0] =>
  array(12) {
    [0] =>array(6) {
      'Field' =>string(2) "id"
      'Type' =>string(7) "int(11)"
      'Null' =>string(2) "NO"
      'Key' =>string(3) "PRI"
      'Default' =>NULL
      'Extra' =>string(14) "auto_increment"
    }
[1] =>array(6) {
      'Field' =>string(7) "a_title"
      'Type' =>string(11) "varchar(20)"
      'Null' =>string(2) "NO"
      'Key' =>string(0) ""
      'Default' =>  NULL
      'Extra' => string(0) ""
    }
    ...
  }
}
***********************************************************
 * $res[0]才是需要便利的二维数组
 * array(12) {
    [0] =>array(6) {
      'Field' =>string(2) "id"
      'Type' =>string(7) "int(11)"
      'Null' =>string(2) "NO"
      'Key' =>string(3) "PRI"
      'Default' =>NULL
      'Extra' =>string(14) "auto_increment"
    }
[1] =>array(6) {
      'Field' =>string(7) "a_title"
      'Type' =>string(11) "varchar(20)"
      'Null' =>string(2) "NO"
      'Key' =>string(0) ""
      'Default' =>  NULL
      'Extra' => string(0) ""
    }
    ...
}
***********************************************************
 * 最后取到的$fields是一维数组
 * array(13) {
 [Key] =>  string(2) "id"
  [0] =>     string(2) "id"
  [1] =>   string(7) "a_title"
  [2] =>  string(9) "a_content"
 ...
}
***********************************************************
*/