<?php 

namespace user;
/**
 * 基础用户类
 */
class BaseUser
{
	
	public $email;

	private $password;

	function __construct($email, $password)
	{
		$this->email = $email;
		$this->password = $password;

	}


}




 ?>