<?php
namespace controller;

use controller;
use controller\BaseController;
use user\BaseUser;
use model\LW_Model;

require dirname(__FILE__) . "/../model/LW_Model.php";

/**
 * 老王的项目控制器
 */
class LWController extends BaseController
{

	public $project_name = "LW";

	// 老王尝试注册方法
	public function doRegist($data) {
		$model = new LW_Model();
		$view_data = $model->doRegist($data);

		$this->view->render($view_data);

	}

	// 老王验证成功方法
	public function doRegistSendToken($data) {
		$model = new LW_Model();
		$view_data = $model->doRegistSendToken($data);

		$this->view->render($view_data);
	}

	// 老王尝试登陆方法
	public function doSign($data) {
		$model = new LW_Model();
		$view_data = $model->doSign($data);

		$this->view->render($view_data);

	}

    // 尝试寻找对手
	public function doAssignAnother($data) {
	    $model = new LW_Model();
	    $view_data = $model->doRequestAssignAnother($data);
//         print_r($view_data);
	    $this->view->render($view_data);

	}

	// 查询对手上次落点
	public function doRequestAnotherPoint($data) {
	    $model = new LW_Model();
	    $view_data = $model->doRequestAnotherPoint($data);

	    $this->view->render($view_data);
	}


	// 更新自己落点
	public function doUpdatePoint($data) {
	    $model = new LW_Model();
	    $view_data = $model->doUpdatePoint($data);

	    $this->view->render($view_data);
	}


	function __construct($view)
	{
	    parent::__construct($view);
	    // 请求分配对手
	    parent::$route['assign_for_another'] = 'doAssignAnother';
	    // 查询对手上次落点
        parent::$route['request_another_point'] = 'doRequestAnotherPoint';
        // 更新自己落点
        parent::$route['update_point'] = 'doUpdatePoint';
	}

}

?>
