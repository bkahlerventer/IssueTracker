<?php
Module::check();

if (!empty($_SESSION['ADMIN_UID']) and is_admin($_SESSION['ADMIN_UID'])) {
	$_ENV['menu']['Administration'] = array(
		'sub' => array(
			'Switch Back' => '?module=admin&action=switch_users&return=true'
		)
	);
}
?>
