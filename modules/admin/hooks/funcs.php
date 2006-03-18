<?php
Module::check();
/**
* Notify all admins by email with message
* 
* @param string $message Message to send to admins
* @returns nothing
*/
function admin_notify($message) {
	$sql = "SELECT email FROM users WHERE admin='t'";
	$emails = $_ENV['dbi']->fetch_all($sql);
	if (is_array($emails)) {
		include_once(_CLASSES_.'mail.class.php');
		if (!is_object($mailer)) {
			$mailer = new MAILER();
			$mailer->set('email_from',_EMAIL_);
		}
		$mailer->subject('Issue Tracker Admin Alert');
		$mailer->to($emails);
		$mailer->message($message);
		$mailer->send();
	}
}
?>
