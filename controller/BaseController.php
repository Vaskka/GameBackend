<?php
namespace controller;

use controller\LWController;
use controller\VaskkaController;
use view\BaseView;

require dirname(__FILE__).'/LWController.php';
require dirname(__FILE__).'/VaskkaController.php';
require dirname(__FILE__).'/../view/BaseView.php';

/**
 * 基础控制器 包含公共的方法和属性
 */
class BaseController
{
	// 项目名称
	public $project_name;

	// View对象
	public $view;

	/**
	 * 路由映射
	 * 0:尝试注册
	 * 	    code: -1->邮箱已被占用, 0->已成功发送验证码, 1->发送验证码失败
	 * 1:向服务器提交验证码
	 *      code: -1->验证码错误, 0->验证正确
	 * 2:尝试注册:
	 *      code: -1->账号或密码错误, 0->登陆成功
	 *
	 */
	protected static $route = [
		// 尝试注册
		'regist' => 'doRegist',
		// 验证成功
		'regist_send_token' => 'doRegistSendToken',
		// 尝试登陆
		'sign' => 'doSign',
// 	    'assign_for_another' => 'doAssignAnother',
// 	    'request_another_point' => 'doRequestAnotherPoint',
// 	    'doUpdatePoint' => 'doUpdatePoint'

	];



	// 使用View对象来实例化控制器
	function __construct($view) {
		$this->view = $view;
	}


	// 尝试注册方法
	protected function doRegist($data) {
		// echo "doRegist";
	}

	// 发送验证码
	protected function doRegistSendToken($data) {
		// echo "doRegistSendToken";
	}

	// 尝试注册
	protected function doSign($data) {
		// echo "doSign";
	}

	// 尝试处理具体逻辑
	protected function doParse($data) {

	}

	/**
	 * 从工程名得到相应控制器
	 * @param $pjt_name 工程名
	 * 未匹配返回基础控制器的实例
	 */
	public static function checkGETToGetController($pjt_name) {

		// 根据工程名返回对应控制器
		switch ($pjt_name) {
			case 'Vaskka':
				return new VaskkaController(new BaseView());
				break;
			case 'LW':
				return new LWController(new BaseView());
				break;
			default:
				// 未匹配返回基础控制器的实例
				return new BaseController(new BaseView());
				break;
		}


	}

	/**
	 * 根据status路由到相应的方法
	 * @param $status 状态
	 * @param $data 数据
	 */
	public function fromGETToRoute($status, $data) {
		foreach (BaseController::$route as $r=>$f) {
			if ($status == $r) {
				$this->$f($data);
			}
		}

	}



}

?>