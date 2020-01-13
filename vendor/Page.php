<?php
/*
created by syob
*/
namespace vendor;
class Page {
    /*
     * @param1 string $url , 基本链接
     * @param2 int $counts , 记录数量
     * @param3 int $pagecount 每页的文章数量
     * @param4 int $page , 起始页码
     * @param5 array $cond , 条件数组
     * return  stirng $click  返回分页字符串
     *
     */
    public static function clickPage(string $url, int $counts, int $pagecount = 10, int $page = 1, array $cond = array(), bool $home = false) {
        //总页数l
        $pages = ceil($counts / $pagecount);
        //前一页
        $prev = $page > 1 ? $page - 1 : 1;
        //后一页
        $next = $page < $pages ? $page + 1 : $pages;
        //路径参数字符串, $pathinfo = index.php?$k=$v&$k=$v&...
        $pathinfo = '';
        foreach ($cond as $k => $v) {
            $pathinfo .= $k . '=' . $v . '&';
        }

        if(!$home){
            //首先添加上一页
            $click = "<li><a href='{$url}?{$pathinfo}page={$prev}'>上一页</a></li>";
            //如果当前页码$page小于7, 此时, 遍历前面的页码, 输出即可
            if ($pages <= 7) {
                for ($i = 1; $i <= $pages; $i++) {
                    if($page == $i){
                        $click .= "<li><a style='color:#000;' href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                    }else{
                        $click .= "<li><a href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                    }
                }
            }
            else {
                //此时总页数$pages大于7, 但是当前页数$page, 小于等于5, 打印出前七页即可
                if ($page <= 5) {
                    for ($i = 1; $i <= 7; $i++) {
                        if($page == $i){
                            $click .= "<li><a style='color:#000;' href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                        }else{
                            $click .= "<li><a href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                        }
                    }
                    //最后追加一个...
                    $click .= "<li><span>...</span></li>";
                }
                else {
                    //此时总页面$pages大于7, 固定保留前两页, 加...
                    $click .= "<li><a href='{$url}?{$pathinfo}page=1'>1</a></li>";
                    $click .= "<li><a href='{$url}?{$pathinfo}page=2'>2</a></li>";
                    $click .= "<li><span>...</span></li>";
                    //此时当前页码, 在最后三页, 不用加...
                    if (($pages - $page) <= 3) {
                        //for循环输出后5页
                        for ($i = $pages - 4; $i < $pages; $i++) {
                            if($page == $i){
                                $click .= "<li><a style='color:#000;' href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                            }else{
                                $click .= "<li><a href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                            }
                        }
                    }
                    else {
                        //此时页码在中间几页, 目前是10, 开始是8, 最后是13:=> 8, 9, 10, 11, 12, 13
                        for ($i = $page - 2; $i <= $page + 2; $i++) {
                            if($page == $i){
                                $click .= "<li><a style='color:#000;' href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                            }else{
                                $click .= "<li><a href='{$url}?{$pathinfo}page={$i}'>{$i}</a></li>";
                            }
                        }
                        //最后加上...
                        $click .= "<li><span>...</span></li>";
                    }


                }
            }
            //最后添加下一页
            $click .= "<li><a href='{$url}?{$pathinfo}page={$next}'>下一页</a></li>";
        }else{
            //添加首页, <li>共有 16 条记录,每页显示 3 条记录， 当前为 1/6</li>-->
            $click = "<li><a href='{$url}?{$pathinfo}page=1'>首页</a></li>";
            $click .= "<li><a href='{$url}?{$pathinfo}page={$prev}'>上一页</a></li>";
            $click .= "<li><a href='{$url}?{$pathinfo}page={$next}'>下一页</a></li>";
            $click .= "<li><a href='{$url}?{$pathinfo}page={$pages}'>末页</a></li>";
            $click .= "<li>共有 {$counts} 条记录,每页显示 {$pagecount} 条记录， 当前为 {$page}/{$pages}</li>";
        }

        return $click;
    }
}