<?php 

/**
 * 发送邮件类
 */
namespace mailer;

use settings\Setting;
use utils\Util;
use PHPMailer\PHPMailer;

require 'mailerlib/PHPMailer.php';
require 'mailerlib/SMTP.php';
require 'settings/Setting.php';
require 'utils/Util.php';


class SendMail
{
	// 验证码
	public $message;	

	// 收件人
	public $to = "";

	function __construct($to) {
		$this->to = $to;
	}

	// 发送验证码，成功返回验证码，失败返回-1
	public function send() {
		$mail = new PHPMailer\PHPMailer();

		$mail->isSMTP();

		$mail->SMTPAuth=true;

		$mail->Host = Setting::$EMAIL_HOST;

		$mail->SMTPSecure = 'ssl';

		$mail->Port = Setting::$EMAIL_PORT;

		$mail->Hostname = Setting::$EMAIL_HOST_NAME;

		$mail->CharSet = Setting::$EMAIL_CHARSET;

		$mail->FromName = 'Vaskka';

		// User
		$mail->Username = Setting::$EMAIL_FROM_ADDRESS;

		// token
		$mail->Password = Setting::$EMAIL_TOKEN;

		$mail->From = Setting::$EMAIL_FROM_ADDRESS;

		$mail->isHTML(true); 

		$mail->addAddress($this->to, Setting::$EMAIL_SUBJECT);

		$mail->Subject = Setting::$EMAIL_SUBJECT;

		$_token = Util::getToken();

		$_message = Util::fromFileGetString(Setting::$MAIL_HTML_BEFORE);
		$_message .= $_token;
		$_message .= Util::fromFileGetString(Setting::$MAIL_HTML_AFTER);


		$mail->Body = $_message;

		$status = $mail->send();

		//简单的判断与提示信息
		if($status) {
		    return $_token;
		}
		else {
			return "-1";   
		}

	}




}

?>