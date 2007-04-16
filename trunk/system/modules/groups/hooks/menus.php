<?php
Module::check();
if (Permission::check('group_manager')) {
	$_ENV['imgs']['menu']['Groups'] = IMGDIR.'group.png';
	$_ENV['menu']['Groups']['Current Groups'] = '?module=groups';
	$_ENV['imgs']['menu']['Current Groups'] = IMGDIR.'group.png';
	$_ENV['menu']['Groups']['New Group'] = '?module=groups&action=new';
	$_ENV['imgs']['menu']['New Group'] = IMGDIR.'new_group.png';
}
?>
