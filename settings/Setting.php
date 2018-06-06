<?php
namespace settings;

/**
 * 各种配置
 */
class Setting
{
	/* config for mysql */
	// mysql 用户名
	const MYSQL_USER = "test";
	// mysql 密码
	const MYSQL_PASSWORD = "czm19990216";
	// mysql url
	const MYSQL_URL = "localhost:3306";

	/* config for send mail */
	// 发送邮件的html文本样例
	private static $MAIL_HTML_PATH = "files/mail.html";
	// 文本前段
	public static $MAIL_HTML_BEFORE = "files/mail_before.html";
	// 文本后端
	public static $MAIL_HTML_AFTER = "files/mail_after.html";

	// 发件地址
	public static $EMAIL_FROM_ADDRESS = "15145051056@163.com";
	// 邮箱token
	public static $EMAIL_TOKEN = "lsy032152";

	// mail 主题
	public static $EMAIL_SUBJECT = "游戏账号注册邮箱验证";
	// mail service host
	public static $EMAIL_HOST = "smtp.163.com";
	// mail host name
	public static $EMAIL_HOST_NAME = "http://www.vaskka.com";
	// mail 字符编码
	public static $EMAIL_CHARSET = "UTF-8";
	// mail 端口号
	public static $EMAIL_PORT = 465;


	/* config for LW */
	// 数据库名称
	public static $LW_DATABASE = "lw_game";
	// 用户数据表
	public static $LW_USER_TABLE = "user";

	// 临时储存email和token的表
	public static $LW_USER_TOKEN_TABLE = "user_token";

	// session表
	public static $LW_SESSION_TABLE = "session";

    /* 用户状态 */
    // 等待匹配
	public static $HOLD_STATUS = "HOLD";

    // 游戏中
    public static $ACTIVE_STATUS = "ACTIVE";



}


?>