<?php

//1.命名空间
//2.调动这个命名空间里面的类利用命名空间的名称可以很快的找到这个类
namespace houdunwang\view;

class Base {
    //1.创建属性
    //2.用来保存分配的变量---需要把分配的变量跟着模板返回
    public $data=[];
    //1.创建属性
    //2.用来保存模板的路径
    public $template;

    /**
     * 分配变量
     * @param $data
     */
    //1.创建方法
    //2.用来分配变量
    public function with($data){
        //把传进来的变量存起来，方便后续的输出
        $this->data=$data;
        //返回一个对象，最后会返回到\houdunwang\core\Boot这个类里面的appRun方法，并且被echo输出出来
        return $this;
    }

    /**
     * 制作模板
     * @return $this
     */
    public function make(){
        //1.组合完整的模板路径
        //2.方便加载模板
        //默认的模板路径："../app/home/view/entry/index.php";
        $this->template = '../app/' . APP . '/view/' . CONTROLLER . '/' . ACTION . '.php';
        //1.返回当前对象，
        //(1)返给\houdunwang\view\View里面的__callStatic
        //(2)View里面的__callStatic再返回给\app\home\controller\Entry里面的index方法(View::make())
        //(3)Entry里面的index方法又返回给\houdunwang\core\Boot里面的appRun方法，在appRun方法用了echo 输出这个对象
        //2.为了触发__toString
        return $this;
    }

    /**
     * 载入模板
     * @return string
     */
    public function __toString() {
        //1.因为$data被传过来的时候是一个数组，把这个数组转为字符串才方便操作
        //2.把键名变成变量名，键值变成变量值
        extract($this->data);
        //1.加载模板
        //2.为了显示默认的页面
        include $this->template;
        //__toString这个方法的时候返回的值必须是有个字符串，列如空字符串
        return '';
    }
}