<?php
//1.命名空间
//2.调动这个命名空间里面的类利用命名空间的名称可以很快的找到这个类
namespace houdunwang\model;
//使用PDO命名空间
use PDO;
//使用PDOException这个命名空间
use PDOException;
//1.创建一个Base类
//2.用来实现连接数据库和获得数据库数据的功能

class Base {
    //保存PDO对象的静态属性为null
    private static $pdo = null;
    //保存表名属性
    //方便一下调用
    private $table;
    //保存where
    //方便下面执行where条件的时候调用
    private $where;
    //1.创建构造方法
    //2.一执行这个类的时候就会自动执行这个方法--自动连接数据库
    public function __construct($table) {
        //调用Base这个类的时候就会自动触发connect这个方法自动连接数据库
        $this->connect();
        //传参之后当前的表单用$table储存
        $this->table = $table;
    }

	/**
	 * 链接数据库
	 */
	private function connect() {
		//如果构造方法多次执行，那么此方法也会多次执行，用静态属性可以把对象保存起来不丢失，
		//第一次self::$pdo为null，那么就正常链接数据库
		//第二次self::$pdo已经保存了pdo对象，不为NULL了，这样不用再次链接mysql了。
		if ( is_null( self::$pdo ) ) {
            //1.设置数据可以的类型，主机，库名
            //2.数据通过functions 函数里面的c函数到配置文件里面调用出来的
            $dsn="mysql:host=".c("database.db_host").";dbname=".c("database.db_name");
            //连接数据库
            $pdo=new PDO($dsn,c("database.db_user"),c("database.db_password"));
            //1.设置错误属性
            //2.把错误的属性设置成异常错误，这样才能被catch捕捉到
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //设置字符集
            $pdo->exec("SET NAMES " .c("database.db_charset"));
            //1.把pdo对象放到静态属性中
            //2.因为静态属性在函数调用过一次后就会有一个初始值，利用初始值我们就不必再重复的连接数据库
            self::$pdo=$pdo;
		}

	}

    /**
     * 获取全部数据
     */
    //创建一个get方法
    //方便app\home\controller\Entry里面的index方法直接调用
    public function get() {
        //用变量$sql储存表单里面的全部数据
        if(is_null($this->where)){
            $sql = "SELECT * FROM {$this->table}";
        }else{
            $sql = "SELECT * FROM {$this->table} where {$this->where}";
        }

        //1.把pdo对象放到静态属性中
        //2.因为静态属性在函数调用过一次后就会有一个初始值，利用初始值我们就不必再重复的连接数据库
        //用变量$result储存当前执行有结果集的操作
        $result = self::$pdo->query( $sql );
        //获得关联数组并用变量$data储存起来
        $data = $result->fetchAll( PDO::FETCH_ASSOC );
        //返回上面获得关联数组并用变量$data储存起来的值
        return $data;
    }


    /**
     * 查询单条数据
     * @param $id
     *
     * @return mixed
     */
    //创建一个find方法并传参数$id
    //但是这个$id无法判断是哪一个表单里面的主键
    public function find($id){
        //先用一个变量把获得的主键储存起来
        //方便下面调用
        $priKey = $this->getPriKey();
        //用变量储存用where条件查看当前表单主键对应的id的结果
        $sql = "SELECT * FROM {$this->table} WHERE {$priKey}={$id}";
        //把当前执行有结果集的操作储存起来
        $data = $this->q($sql);
        //返回变量$data储存的数据被转为一维数组后的结果
        return current($data);
    }

    /**
     * 查找当前表的信息
     * @param $post
     * @return mixed
     */
    public function save($post){
        //查询当前表信息
        //储存当前表结构执行有结果集的操作
        $tableInfo = $this->q("DESC {$this->table}");
        //让当前表的字段为空数组
        $tableFields = [];
        //获取当前表的字段 [title,click]
        foreach ($tableInfo as $info){

            $tableFields[] = $info['Field'];
        }
        //循环post提交过来的数据
        //Array
//		(
//			[title] => 标题,
//			[click] => 100,
//			[captcha] => abc,
//		)
        $filterData = [];
        foreach ($post as $f => $v){
            //如果属于当前表的字段，那么保留，否则就过滤
            if(in_array($f,$tableFields)){
                $filterData[$f] = $v;
            }
        }
//      Array
//		  (
//			[title] => 标题,
//			[click] => 100,
//		)

        //获得数组中的键名
        $field = array_keys($filterData);
        //将数组转为字符串
        $field = implode(',',$field);
        //获得数组中的键值
        $values = array_values($filterData);
        //数组转为字符串以后，用'"'把字符隔开
        $values = '"' . implode('","',$values)  . '"';
        //向文章表里面录入内容，$this->table就比如为'arc',{$field}为要修改的的键值
        $sql = "INSERT INTO {$this->table} ({$field}) VALUES ({$values})";
        return $this->e($sql);
    }

    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function update($data){
        //用if判断
        if(!$this->where){
            //此处说明delete必须有where条件
            //应为只有id匹配到对应的内容才能执行编辑功能
            exit('update必须有where条件');
        }
        //Array
//		(
//			[title] => 标题,
//			[click] => 100,
//		)
        //设置变量为空
        $set = '';
        //找到数据里面对应的键值
        foreach ( $data as $field => $value ) {
            //链接键名所对应的键值
            //提示你必须为字符串
            $set .= "{$field}='{$value}',";
        }
        //右截取','后面的内容
        $set = rtrim($set,',');
        //使用where条件编辑表单里id对应的内容
        //$this->table就是指表单里面的'title','click'
        //$this->where就是指表单里面的要编辑的内容所对应的id
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->where}";
        //返回当前执行没有结果集的操作
        return $this->e($sql);
    }


    /**
     * where条件
     * @param $where
     * @return $this
     */
    public function where($where){
        //储存当前对应的id
        $this->where = $where;
        //返回当前储存对应的id
        return $this;
    }

    /**
     * 摧毁数据
     */
    public function destory(){
        //用if判断
        if(!$this->where){
            //此处说明delete必须有where条件
            //应为只有id匹配到对应的内容才能执行删除功能
            exit('delete必须有where条件');
        }
        //录入内容并用变量储存$this->table就是指表单里面的'title','click'
        //$this->where就是指表单里面的要删除的内容所对应的id
        $sql = "DELETE FROM {$this->table} WHERE {$this->where}";
        //返回当前执行没有结果集的操作
        //$this-指向的是e()这个方法
        return $this->e($sql);
    }


    /**
     * 获得主键
     */
    private function getPriKey(){
        //用一个变量储存当前获得表单的结构
        $sql = "DESC {$this->table}";
        //用变量$data储存当前执行有结果集的操作
        $data = $this->q($sql);
        //让主键为空
        $primaryKey = '';
        //找到表单里面的所有键值
        foreach ($data as $v){
            //查看有没有键名为PRI
            if($v['Key'] == 'PRI'){
                //根据键名为PRI来判断id是哪一个表单里面的id
                $primaryKey = $v['Field'];
                //结束此循环直接执行下一个循环
                break;
            }
        }
        //返回主键值
        return $primaryKey;
    }



    /**
     *执行有结果集的操作
     * @param $sql
     * @return mixed
     */

    //q($sql)执行有结果集的操作
    public function q($sql){

            //1.接收数据的结果
            //2.q($sql)执行有结果集的操作
            $result = self::$pdo->query( $sql );
            //返回获得的所有数据
            return $result->fetchAll( PDO::FETCH_ASSOC );

    }

    /**
     *执行没有结果集的操作
     * @param $sql
     * @return mixed
     */
    public function e( $sql ) {

            //执行没有结果集的操作并储存此操作
            $afRows = self::$pdo->exec( $sql );
            //返回此操作的结果
            return $afRows;

    }
}