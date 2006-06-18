<?php
Module::check();
if (Permission::check('user_manager')) {
	$_ENV['imgs']['menu']['Users'] = IMGDIR.'user.png';
	$_ENV['menu']['Users']['Current Users'] = '?module=user';
	$_ENV['imgs']['menu']['Current Users'] = IMGDIR.'user.png';
	$_ENV['menu']['Users']['New User'] = '?module=user&action=new';
	$_ENV['imgs']['menu']['New User'] = IMGDIR.'new_user.png';
}
?>

