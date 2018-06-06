<?php
/**
 * http处理类
 */

namespace model;

use model\BaseModel;
use settings\Setting;


require dirname(__FILE__) . "/BaseModel.php";

class LW_Model extends BaseModel
{

	// session表名
	private $session_table_name;



	/**
	 * 尝试注册
	 * @param $data 字典数据（email=>"email", password=>"password"）
	 * @return 0 for 验证码发送成功; -1 for email 已经存在; 1 for 验证码发送失败;
	 */
	public function doRegist($data) {
		$email = $data["email"];
		if ($this->checkIsExist($email)) {
			return array('code' => "-1", 'token' => "-1", 'msg'=>'Email has been existed, please change a email.');
		}

		$token = $this->toEnsurePrimaryKeyAvailable($email);
		if ($token == -1) {
			return array('code' => "1", 'token' => "-1", 'msg'=>'send mail error, please change a email.');
		}

		// // 将注册的token储存起来
		// $token_data = [
		// 	"email"=>$email,
		// 	"token"=>$token,
		// ];
		// $this->insertUserTokenTable($token_data);

		return array('code' => 0, 'token' => $token, 'msg'=>'token send success~');

	}


	/**
	 * 验证码验证通过 (前端处理模式)
	 * @param $data 字典数据（email=>"email", password="password" name="name"）
	 * @return 0 for 登陆成功; 1 for 登陆失败;
	 */
	public function doRegistSendToken($data) {

		// 插入用户表
		$this->insertUserTable($data);

		// 验证通过进行注册
		$data_view =  $this->doSign($data);

        $this->initMysql();
        $query = "SELECT name FROM " . Setting::$LW_USER_TABLE . " WHERE email='" . $data["email"] . "';";
        $raw_data = mysqli_query($this->mysql_conn, $query);
        $name = mysqli_fetch_array($raw_data);
        $data_view["name"] = $name["name"];

        return $data_view;

	}

	// 初始化session表
    private function initSessionTable($email) {
        $now = date("Y/m/d H:i:s");
        $this->mysql_conn = mysqli_connect($this->mysql_url, $this->mysql_user, $this->mysql_password, $this->database);
        mysqli_query($this->mysql_conn, "INSERT INTO " . $this->session_table_name . " (email, update_time, another_email, justnow_point, stat, turn)" ." VALUES ('" . $email . "', '" . $now ."', 'E', 'E', 'HOLD', 'undefined') ON DUPLICATE KEY UPDATE email=VALUES(email), update_time=VALUES(update_time), another_email=VALUES(another_email), justnow_point=VALUES(justnow_point), stat=VALUES(stat), turn=VALUES(turn);");

    }

    // 更新session时间
    private function updateSessionTime($email) {
        $now = date("Y/m/d H:i:s");
        $this->mysql_conn = mysqli_connect($this->mysql_url, $this->mysql_user, $this->mysql_password, $this->database);
        mysqli_query($this->mysql_conn, "UPDATE " . Setting::$LW_SESSION_TABLE . " SET update_time='" . $now . "' WHERE email='" . $email . "';");
    }


	/**
	 * 尝试登陆
	 * @param $data 字典数据 (email=>"email", password=>"password")
	 * @return  0 for 登陆成功; 1 for 登陆失败
	 */
	public function doSign($data) {
		$email = $data["email"];
		$password = $data["password"];

		$this->initMysql();
		$raw_result = mysqli_query($this->mysql_conn, "SELECT * FROM " . $this->user_table_name . " WHERE email=" . "'" . $email . "'" . "AND password=" . "'" . $password . "';");

		$exist = mysqli_fetch_array($raw_result, MYSQLI_ASSOC);

		$view_data = array();



		if ($exist["password"] == $password) {
			// 注册成功插入session表
            $this->initSessionTable($email);

			$view_data["code"] = 0;
			$view_data["msg"] = "登陆成功";
			$view_data["name"] = $data["name"];

			return $view_data;
		}

		$view_data["code"] = 1;
		$view_data["msg"] = "登陆失败";
		$view_data["name"] = "-1";
		return $view_data;

	}


	/**
	 * 请求分配对手
	 * @param dict $data
	 * @return 1 for 未找到对手(已发出邀请)  0 for 找到对手  turn :0 for first 1 for next 2 for undefined
	 * code:<code>0</code> for 已找到对手
	 *      <code>1</code> for 正在发出邀请
	 *      <code>-1</code> for 仍在匹配中
	 *
	 * turn:<code>first</code> for 先手
	 *      <code>behind</code> for 后手
	 *      <code>undefined</code> for 为找到匹配
	 *
	 * email:<code>1</code> for 未找到匹配
	 *       <code>"email"</code> for 已找到email="email"的对手
	 *
	 * name:<code>1</code> for 未找到匹配
	 *      <code>"name"</code> for 已找到name="name"的对手
	 *
	 */
    public function doRequestAssignAnother($data) {
        $this->updateSessionTime($data["email"]);
        // 先检查自己是否接到邀请
        $this->initMysql();
        $query = "SELECT another_email FROM " . $this->session_table_name . " WHERE email='" . $data["email"] . "';";
        $raw_result = mysqli_query($this->mysql_conn, $query);
        $result = mysqli_fetch_array($raw_result, MYSQLI_ASSOC);

        if ($result["another_email"] == "E") {
            // 自己未接到邀请 尝试发出邀请
            $query = "SELECT email FROM " . $this->session_table_name . " WHERE stat='HOLD' AND email!='" . $data["email"] . "';";
            $raw_result = mysqli_query($this->mysql_conn, $query);
            if (!$raw_result) {
                // 无人可以接到邀请，返回仍在匹配
                return ['code'=>'-1', 'turn'=>'undefined', 'email'=>'1', 'name'=>'1'];
            }

            // 有人可以接到邀请
            $result = mysqli_fetch_array($raw_result, MYSQLI_ASSOC);
            // 更改对方的another_email为自己的email
            mysqli_query($this->mysql_conn, "UPDATE " . Setting::$LW_SESSION_TABLE . " SET another_email='" . $data["email"] . "' WHERE email='" . $result["email"] . "';");
            // 自己的先后手变为先手
            mysqli_query($this->mysql_conn, "UPDATE " . $this->session_table_name . " SET turn='first' WHERE email='" . $data["email"] . "';");

            return ['code'=>'1', 'turn'=> 'first', 'email'=>'1', 'name'=>'1'];
        }


//         print_r($result);
        $result_email = $result["another_email"];

        // 找到自己的stat状态为ACTIVE
        mysqli_query($this->mysql_conn, "UPDATE " . $this->session_table_name . " SET stat='ACTIVE' WHERE email='" . $data["email"] . "';");
        // 更改对方对手信息
        mysqli_query($this->mysql_conn, "UPDATE " . $this->session_table_name . " SET another_email='" . $data["email"] . "' WHERE email='" . $result["another_email"] . "';");
        // 查询name
        $query = "SELECT name FROM " . $this->user_table_name . " WHERE email='" . $result["another_email"] . "';";
        $raw_result = mysqli_query($this->mysql_conn, $query);
        $result = mysqli_fetch_array($raw_result, MYSQLI_ASSOC);

        $raw_result = mysqli_query($this->mysql_conn, "SELECT turn FROM " . $this->session_table_name . " WHERE email='" . $data["email"] . "';");

        $result_turn = mysqli_fetch_array($raw_result, MYSQLI_ASSOC);

        // 若自己不是first改为后手
        if ($result_turn["turn"] != "first") {
            mysqli_query($this->mysql_conn, "UPDATE " . $this->session_table_name . " SET turn='behind' WHERE email='" . $data["email"] . "';");

        }

        return ['code'=>'0', 'turn'=>$result_turn["turn"], 'email'=>$result_email, 'name'=>$result["name"]];

    }


    /**
     * 查询对手的上次落点
     * @param dict $data 传入的字典数据 email=>"email"
     * @return list 返回查询结果
     */
    public function doRequestAnotherPoint($data) {
        $this->updateSessionTime($data["email"]);
        $this->initMysql();

        $query = "SELECT another_email FROM " . $this->session_table_name . " WHERE email='" . $data["email"] . "';";
        $raw_data = mysqli_query($this->mysql_conn, $query);

        // 获得对手email
        $result = mysqli_fetch_array($raw_data);

        // 用对手email查询对手上次落点
        $query = "SELECT justnow_point FROM " . $this->session_table_name . " WHERE email='" . $result["another_email"] . "';";
        $raw_data = mysqli_query($this->mysql_conn, $query);

        $result = mysqli_fetch_array($raw_data);

        return ["code"=>"0", "point"=>$result["justnow_point"]];

    }


    /**
     * 更新自己落点位置
     * @param dict $data email=>"email", point=>"point"
     * @return list 状态
     */
    public function doUpdatePoint($data) {
        $this->updateSessionTime($data["email"]);
        $this->initMysql();

        $query = "UPDATE " . $this->session_table_name . " SET justnow_point='" . $data["point"] . "' WHERE email='" . $data["email"] . "';";
        mysqli_query($this->mysql_conn, $query);

        return ["code"=>"0", "status"=>"success"];
    }


	function __construct()
	{
		$this->project_name = "LW";
		$this->user_table_name = Setting::$LW_USER_TABLE;
		$this->database = Setting::$LW_DATABASE;


		$this->session_table_name = Setting::$LW_SESSION_TABLE;

	}





}

?>