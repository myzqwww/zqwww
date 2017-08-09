<?php
//1.命名空间的名称
//2.如果想要调动这个命名空间里面的类利用命名空间的名称可以很快的找到这个类
namespace houdunwang\core;

/**
 * Class Controller 父类，当积累继承这个类的时候可以重复的调用这个类里面的属性和方法，减少代码量
 * @package houdunwang\core
 */
//创建一个类方法Controller
class Controller{
    //1.创建属性
    //2.给$url一个默认的值：当$url没有传参的时候就会返回上一级的历史记录并且刷新
    private $url = 'window.history.back()';
    //1.创建属性
    //2.用来保存模板的路径，方便我们加载模板
    private $template;
    //1.创建属性
    //2.用来保存提示用户的信息，提高用户的使用体验
    private $msg;
    //1.创建方法
    //2.实现跳转的功能
    /**
     * 跳转
     * @param $url
     * @return $this
     */
    protected function setRedirect($url){
        //1.给$this->url赋值
        //2.当$url存在的时候页面就会跳转到$url这个页面，否则就会是默认值--返回上一级的页面
        $this->url = "location.href='{$url}'";
        //1.返回这个类
        //2.最终会被返回到houhunwang\core\run 这个方法里，并且echo这个类，执行__tostring这个方法
        return $this;
    }
    //1.创建方法
    //2.用来实现提示用户的功能
    //3.当用户在页面操作某项功能并且成功的时候就会提示用户操作成功--比如添加留言
    /**
     * 成功提示
     * @param $msg
     * @return $this
     */
    protected function success($msg){
        //1.把需要提示的信息保存到$this->msg这个属性里
        //2.方便后续的调用
        $this->msg = $msg;
        //1.组合模板路径
        //2.当用户操作成功的时候会加载模板提示用户操作成功
        $this->template = './view/success.php';
        //1.返回这个类
        //2.最终会被返回到houhunwang\core\run 这个方法里，并且echo这个类，执行__tostring这个方法
        return $this;
    }

    /**
     * 失败提示
     * @param $msg
     * @return $this
     */
    protected function error($msg){
        //1.把需要提示的信息保存到$this->msg这个属性里
        //2.方便后续的调用
        $this->msg = $msg;
        //1.组合模板路径
        //2.当用户操作失败的时候会加载模板提示用户操作失败
        $this->template = './view/success.php';
        //1.返回这个类
        //2.最终会被返回到houhunwang\core\run 这个方法里，并且echo这个类，执行__tostring这个方法
        return $this;

    }
    //1.创建方法
    //2.当这个类被echo输出的时候就会自动执行这个方法
    public function __toString() {
        //1.加载模板
        //2.当用户操作成功的时候会加载模板提示用户操作成功
        include $this->template;
        //__toString这个方法必须要返回一个字符串，比如空字符串
        return '';
    }


}