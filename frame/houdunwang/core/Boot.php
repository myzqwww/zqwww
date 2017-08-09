<?php


namespace houdunwang\core;

/**
 * 框架启动类
 * Class Boot
 * @package houdunwang\core
 */
class Boot {

	public static function run(){
		//1.注册错误处理
        //2.调用当前handleError()方法来捕获错误
		self::handleError();
		//1.初始化框架
        //2.就是开启session
		self::init();
		//执行应用
		self::appRun();

	}

    /**
     * 创建一个头部共用的方法handleError()
     * 此处代码需要百度composer进去后点击安装列表，导航输入whoops一般选择第一个filp/whoops进去
     */
    //调用此方法来实现错误处理
	private static function handleError(){
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();
	}

    /**
     * 执行运用
     */
    private static function appRun(){
        //1.当有get传参的时候$s的值就是传参的值，否则默认值就是"home/entry/index"；
        //2.home表示前台应用，entry表示控制器的名称，index表示控制器里面的方法名
        $s = isset($_GET['s']) ? strtolower($_GET['s']) : 'home/entry/index';
        //1.把$s的值通过"/"分界转换成数组
        //2.这方方便数据的调用
        $arr = explode('/',$s);
        //打印数组$arr
        //p($arr);
        //以下为打印出来的数据(这是一个列子参考)
//        Array
//        (
//            [0] => home
//            [1] => entry
//            [2] => index
//)
        //1.把应用比如："home"定义为常量APP
        //2.在houdunwang/view/View.php文件里的View类的make方法组合模板路径，需要用的应用比如:home的名字
        //home是默认应用，有可能为admin后台应用，所以不能写死home
        define('APP',$arr[0]);
        //1.定义一个CONTROLLER常量
        //2.组合模板的时候用来指定控制器的文件目录
        define('CONTROLLER',$arr[1]);
        //1.定义一个ACTION常量
        //2.组合模板的时候用来指定具体的文件名
        define('ACTION',$arr[2]);
        //组合类名：
        //1.通过命名空间找到那个类
        //2.默认需要组合的类名：\app\home\controller\Entry
        $className = "\app\\{$arr[0]}\controller\\" . ucfirst($arr[1]);
        //1.实例化这个类
        //2.并且调用默认的index方法
        echo call_user_func_array([new $className,$arr[2]],[]);
    }

    /**
     * 初始化
     */

    //1.创建方法
    //2.初始化框架，先设置并且准备好以后能用到的代码
    private static function init(){
        //设置时区为默认的东八区
        date_default_timezone_set("PRC");
        //1.开启session
        //2.因为这个来是框架的启动类,只需要在这里开启session框架内的有关联的类的session都会被开启
        //判断有没有session_id,如果没有的话才会执行后面的代码,就是开启session
        session_id() || session_start();
        //设置时区
        date_default_timezone_set('PRC');
        //1.创建常量
        //2.判断用户使用post的请求方式来点击的提交按钮
        define('IS_POST',$_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
    }
}