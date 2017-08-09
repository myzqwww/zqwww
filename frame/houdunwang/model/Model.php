<?php
//1.命名空间
//2.调动这个命名空间里面的类利用命名空间的名称可以很快的找到这个类
namespace houdunwang\model;
class Model {
    //1.创建一个__callStatic静态方法
    //2.当使用未找到的方法的时候会自动执行此方法
    public static function __callStatic( $name, $arguments ) {
        $className = get_called_class();
        //system\model\Arc
        //strrchr字符串截取 变成 \Arc
        //ltrim 去除左边的\ 变成 Arc
        //strtolower 变成 arc
        $table = strtolower(ltrim(strrchr($className,'\\'),'\\'));
        //1.实例化Base这个类
        //2.调用$name这个方法，把值返回到Entry这个控制器里
        return call_user_func_array([new Base($table),$name],$arguments);
    }
}