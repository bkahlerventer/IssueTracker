<?php
Module::check();
if (Permission::check('category_manager')) {
	$_ENV['imgs']['menu']['Categories'] = IMGDIR.'category.png';
	$_ENV['menu']['Categories']['Current Categories'] = '?module=category';
	$_ENV['imgs']['menu']['Current Categories'] = IMGDIR.'category.png';
	$_ENV['menu']['Categories']['New Category'] = '?module=category&action=new';
	$_ENV['imgs']['menu']['New Category'] = IMGDIR.'new_category.png';
}
?>
