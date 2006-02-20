<?php
/**
* List existing categories
*/
module_check();
if (permission_check('category_manager')) {
	$links[] = array(
		'txt' => 'Back to Administration',
		'url' => '?module=admin',
		'img' => $_ENV['imgs']['back']
	);
	$links[] = array(
		'txt' => 'New Category',
		'url' => '?module=categories&action=new',
		'img' => $_ENV['imgs']['category']
	);
	$sql = "SELECT cid,category FROM categories ORDER BY category";
	$categories = $dbi->fetch_all($sql,'array');
	$smarty->assign('categories',$categories);
	module_template('categories','list.tpl');
} else {
	redirect();
}
?>
