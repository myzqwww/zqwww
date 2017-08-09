<?php
/**
 * 打印函数
 * @param $var
 */
function p($var){
	echo '<pre style="background: #ccc;padding: 10px;border-radius: 5px;">';
	print_r($var);
	echo '</pre>';
}

//c('database.db_name');
//c('captcha.length');
function c($path){
	$arr = explode('.',$path);
	//$arr = ['database','db_name'];
	$config = include '../system/config/' . $arr[0] . '.php';
	return isset($config[$arr[1]]) ? $config[$arr[1]] : NULL;
}


function go($url){
    header('Location:' . $url);
    exit;
}




