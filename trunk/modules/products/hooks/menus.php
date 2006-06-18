<?php
Module::check();
if (Permission::check('product_manager')) {
	$_ENV['imgs']['menu']['Products'] = IMGDIR.'product.png';
	$_ENV['menu']['Products']['Current Products'] = '?module=product';
	$_ENV['imgs']['menu']['Current Products'] = IMGDIR.'product.png';
	$_ENV['menu']['Products']['New Product'] = '?module=product&action=new';
	$_ENV['imgs']['menu']['New Product'] = IMGDIR.'new_product.png';
}
?>

