<?php
//1.命名空间
//2.调动这个命名空间里面的类利用命名空间的名称可以很快的找到这个类
namespace houdunwang\view;
//创建一个类的方法View
class View{
    /**
     * 当使用未找到的方法的时候会自动执行此方法
     * @param $name 方法的名字
     * @param $arguments 传过来的参数
     */
    public static function __callStatic($name, $arguments){
        //实例化Base这个类，并且调用$name这个方法，把值返回到Entry这个控制器里
        return call_user_func_array([new Base(),$name],$arguments);
    }
}