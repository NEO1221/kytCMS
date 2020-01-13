<?php
/*
created by syob
*/
namespace core;
class Model {

    protected $dao;
    protected $fields = array();

    public function __construct() {
        $var = $this->table;
        global $config;
        $this->dao = new Dao($config['database']);
        $this->getFields();
    }

    protected function exec($sql) {
        $this->dao->dao_exec($sql);
    }

    public function getLastId() {
        $this->dao->dao_insert_id();
    }

    protected function query(string $sql, $only = true) {
        return $this->dao->dao_query($sql, $only);
    }

    protected function getTable(string $table = '') {
        global $config;
        $table = empty($table) ? $this->table : $table;
        return $config['database']['prefix'] . $table;
    }
    //主键保存数组
    private function getFields() {
        $sql   = 'desc ' . $this->getTable();
        $res[] = $this->query($sql, 0);
        //$res看最后的数据结构]
        foreach ($res[0] as $value) {
            $this->fields[] = $value['Field'];
            if ($value['Key'] == "PRI") {
                $this->fields['Key'] = $value['Field'];
            }
        }
    }
    //根据主键查询数据
    public function getById($id) {
        $sql = "select * from {$this->getTable()} where {$this->fields['Key']} = {$id}";
        return $this->query($sql);
    }
    public function deleteById($id) {
        if (!isset($this->fields['Key'])) return false;
        $sql = "delete from {$this->getTable()} where {$this->fields['Key']} = {$id}";
        return $this->exec($sql);
    }
    /*
     * 有主键的自动更新
     * @param1 int $id，主键值
     * @param2 array $data，要更新的数组，里面必须带主键字段
     * @return mixed ，成功返回受影响的行数（可能是0行），失败（错误）返回false
    */
    public function autoUpdate(int $id, array $data) {
        $where = ' where ' . $this->fields['Key'] . ' = ' . $id;
        $sql   = "update {$this->getTable()} set ";
        foreach ($data as $k => $v) {
            $sql .= $k . "='{$v}',";
        }
        $sql = trim($sql,',') .$where;
        return  $this->exec($sql);
    }

    //自动插入信息
    public function autoInsert($data){
        $in_fields = $in_values = '';
        //$value,就是字段值 id name ...
        //insert into table value (key='value',key2='value2',...)
//        $var= $this->fields;
        foreach($this->fields as $key=>$value){
            if($key == 'Key') continue;
            if(array_key_exists($value,$data)){
                $in_fields .= $value. ',';
                $in_values .= "'".$data[$value]."',";
            }
        }
        $in_fields= rtrim($in_fields,',');
        $in_values= rtrim($in_values,',');

        $sql = "insert into {$this->getTable()} ({$in_fields}) value ({$in_values}) ";
        return $this->exec($sql);
    }
    //获取该表的所有的数据
    public function getAllNum(){
        $sql = "select count(*) c from {$this->getTable()}";
        $res = $this->query($sql);
        return $res['c'];
    }

    //写入测试数据,谨慎启用
    public function add_info_test() {
        //插入用户信息
        $art_cat = array(1,4,5,6,13,14,15);
        for ($i=200; $i<=150; $i++) {
            $admin = 'admin'.$i;
            $password = md5($admin);
            $sql = "insert into b_user  values(null,'{$admin}','{$password}',unix_timestamp(),'0')";
            $this->exec($sql);
            echo '成功插入了admin'.$i.'的数据<br/>';
        }
        //插入文章信息
        for($i= 13 ; $i<=100 ; $i++){
            $rand = mt_rand(0,6);
            $text = "第{$i}测试文章内容, 
            现场要求
① 禁止在10℃以下的现场温度情况下进行安装。
② 所有的底层地面必须是干燥的，扁平的，无裂缝的，构造合理的，并且是干净的。无灰尘，涂料，石蜡，润滑油，油脂，沥青，腐旧的粘合剂和其他外来杂质。
③ 橡胶地板采用无缝拼接，拼接接缝平直、光滑、粘结牢固，外观无明显色差。
④ 所有的硬化处理，淬水处理和破坏性化合物只能必须通过机械方法来加以去除。
⑤ 底层地面的湿度情况应低于2.5%，湿度高于2.5%的底层地面不得予以推荐。若出现此类湿度高于2.5%的情况，则应依照工业标准进行防水处理工作。
⑥ 5毫米以内的表面不平度可以底涂后用自流平找平。";
            $sql = "insert into b_article values(null,'第{$i}篇测试文章标题','{$text}',$art_cat[$rand],1,'admin',unix_timestamp(),2,1,'image20200109092320WWGROD.jpg','thumb_image20200109092320WWGROD.jpg',0,0)";
            $this->exec($sql);
        }
    }
    //获取所有的分类
    public function getAllCategories() {
        $sql        = " select c.*,a.a_count from {$this->getTable()} c
                        left join (select c_id,count(*) a_count from {$this->getTable('article')} group by c_id) a
                        on c.id = a.c_id
                        order by c.c_sort desc";
        $categories = $this->query($sql, 0);
        return $this->noLimitCategory($categories);
    }
    /*
     *  组装分类栏目, 为分类安排逻辑关系
     * @param1 array $categories 是没有逻辑关系的所有数据
     * @param2 int $parent_id 查找该id下的所有分类
     * @param3 int $level  指定该分类的层级
     * @param4 int $stop  表示不获取的分类
     * @return array $list  最后处理好的数组
     *
     * $parent_id要小于等于stop,要不然起不到限制作用
     *
     * $categories 示例数据如下
     * array(7) {
          [0] =>array(4) {
            'id' =>string(1) "1"
            'c_name' =>string(8) "IT技术"
            'c_sort' =>string(4) "2223"
            'c_parent_id' =>string(1) "0"
          }}
     * */
    public function noLimitCategory($categories, $parent_id = 0, $level = 0,$stop = 0) {
        static $list = array();
        foreach ($categories as $cat) {

            //如果该栏目id等于stop, 那么不查找该id.
            if($cat['id'] == $stop) continue;

            if ($cat['c_parent_id'] == $parent_id) {
                //把层级值存入没有逻辑的数组中
                $cat['level'] = $level;
                //一个分类,占一个数组, 数组的key, 就是分类的id ,数组的值 ,是一个包含该分类所有信息的二维数组
                //把当前分类数组, 存入以分类主键id为KEY的, 二维数组中
                //这时存入数组的都是, $parent_id 为0的数据, 也就是一个顶级分类的数据, 存放到静态$list, 最终数组里
                $list[$cat['id']] = $cat;
                //此时进行递归, 把当前顶级分类的id, 做为父id, 查找该父id下的, 所有分类
                $this->noLimitCategory($categories, $cat['id'], $level + 1,$stop);
            }
        }
        return $list;
    }
    //是否查找delete

}

