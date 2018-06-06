<?php
namespace view;

/**
 * 基础视图
 * 公共的方法和属性
 * 
 */
class BaseView
{

	
	
	/**
	 * 基础渲染
	 * @param $data 需要渲染的数据(json)  
	 *
	 */
	public function render($data) {
		echo json_encode($data);
	}
	
}



?>