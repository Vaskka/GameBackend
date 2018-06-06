<?php
/*
Vaskka 处理类
*/

namespace model;

use settings\Setting;


require dirname(__FILE__) . "/LW_Model.php";

/**
 * Vaskka 处理类
 */
class VaskkaModel extends LW_Model
{

	/**
	 * 更新用户分数
	 */
	public function doUpdataScore($data)
	{
		# code...
	}


	function __construct()
	{
		$this->project_name = "Vaskka";
		$this->user_table_name = Setting::$LW_USER_TABLE;
		$this->database = Setting::$LW_DATABASE;

	}


}

?>