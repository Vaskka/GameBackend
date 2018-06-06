<?php 
/**
 * 工具类
 * 封装常用函数
 */
namespace utils;


class Util
{
	// 从文件得到字符串
	public static function fromFileGetString($filePath) {
		$file = fopen($filePath, "r");
		return fread($file, filesize($filePath));
	}

	// 得到验证码
	public static function getToken() {
		// 根据当前时间的MD5作为验证码
		return md5(time() + rand());
	}


}

?>