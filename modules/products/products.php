<?php
/**
* List existing products
*/
module_check();
if (permission_check('product_manager')) {
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
    $smarty->assign('products',product_list());
	module_template('products','list.tpl');
} else {
  redirect();
}
?>
