<?php
/**
 * Model 基类
 * 公共的方法和属性
 * 用户表默认采取 email -> password 映射
 */
namespace model;

use mailer\SendMail;
use settings\Setting;

require dirname(__FILE__) . "/../mailer/SendMail.php";


class BaseModel
{
	// 用户列表
	public $user = [];

	// 工程名
	protected $project_name;

	// 数据库名
	protected $database;

	// 用户表名
	protected $user_table_name;

	// 数据库链接url
	protected $mysql_url = Setting::MYSQL_URL;

	// 数据库用户名
	protected $mysql_user = Setting::MYSQL_USER;

	// 数据库密码
	protected $mysql_password = Setting::MYSQL_PASSWORD;

	// 数据库连接指针
	protected $mysql_conn;


	function __construct()
	{
		$this->mysql_conn = mysqli_connect($this->mysql_url, $this->mysql_user, $this->mysql_password, $this->database);

	}

	// 获得数据库名
	public function getDatabaseName() {
		return $this->datebase;
	}

	// 获得工程名
	public function getProjectName() {
		return $this->project_name;
	}

	// 获得用户表名
	public function getUserTableName() {
		return $this->user_table_name;
	}

	/**
	 * 初始化数据库链接
	 */
	protected function initMysql() {
	    $this->mysql_conn = mysqli_connect($this->mysql_url, $this->mysql_user, $this->mysql_password, $this->database);

	}


	// 检查是否存在此用户
	protected function checkIsExist($email) {
		$result = $this->checkIsPrimaryKeyExist($email);

		return $result;

	}

	// 默认的email为主键检查用户是否存在
	protected function checkIsPrimaryKeyExist($email) {

	    $this->initMysql();

		$raw_email_result = mysqli_query($this->mysql_conn, "SELECT * FROM " .$this->user_table_name . " WHERE email=" . "'" . $email . "';");

		$exist = mysqli_fetch_array($raw_email_result, MYSQLI_ASSOC);

		// 存在返回true
		if ($exist) {
			return true;
		}

		return false;
	}

	/**
	 * 检查token正确性 (后台检查)
	 * @param $data email=>"email", token=>"token"
	 */
	protected function checkToken($data) {
		$email = $data["email"];
		$token = $data["token"];

		$this->initMysql();
		$raw_email_result = mysqli_query($this->mysql_conn, "SELECT * FROM " . Setting::$LW_USER_TOKEN_TABLE . " WHERE email="."'" . $email . "';");

		$exist = mysqli_fetch_array($raw_email_result, MYSQLI_ASSOC);
		// mysql_close($this->mysql_conn);

		// 返回true
		if ($exist["token"] == $token) {
			return true;
		}

		return false;
	}

	// 验证主键唯一性 成功发送返回true
	protected function toEnsurePrimaryKeyAvailable($to) {

		$send_email = new SendMail($to);

		return $send_email->send();


	}

	/**
	 * 插入user表
	 * @param $data 字典数据 email=>"email", password=>"password", name=>"name"
	 */
	protected function insertUserTable($data) {
		$email = $data["email"];
		$password = $data["password"];
		$name = $data["name"];

		$this->initMysql();
		mysqli_query($this->mysql_conn, "INSERT INTO " . $this->user_table_name . " (email, password, name) VALUES ('" . $email . "', '" . $password . "', '". $name ."');");


	}


	/**
	 * 插入user_token表, 储存注册时的token，方便验证 (后台检查模式)
	 * @param $data 字典数据 email=>"email", token=>"token";
	 */
	protected function insertUserTokenTable($data) {
		$email = $data["email"];
		$token = $data["token"];
		$this->initMysql();
		mysqli_query($this->mysql_conn, "INSERT INTO " . Setting::$LW_USER_TOKEN_TABLE . " VALUES ('" . $email . "', '" . $token . "');");

	}


	/**
	 * 更新得分
	 * @param unknown $data email=>"email", $score=>"score";
	 */
    public function doUpdateScore($data) {
        $email = $data["email"];
        $score = $data["score"];

        $this->initMysql();
        mysqli_query($this->mysql_conn, "UPDATE " . Setting::$LW_USER_TABLE . " SET score=" . $score . " WHERE email='" . $email . "';");


    }

	// 主要游戏后端逻辑 (下一版本推出)
	protected function parse() {

	}


}

?>