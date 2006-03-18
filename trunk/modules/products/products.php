<?php
Module::check();
if (Permission::check('product_manager')) {
	$links[] = array(
		'txt' => 'Back to Administration',
		'url' => '?module=admin',
		'img' => $_ENV['imgs']['back']
	);
	$links[] = array(
		'txt' => 'New Product',
		'url' => '?module=admin&action=products&subaction=new',
		'img' => $_ENV['imgs']['product']
	);
    $_ENV['tpl']->assign('products',product_list());
	Module::template('products','list.tpl');
} else {
	redirect();
}
?>
