<?php
Module::check();
if (Permission::check('product_manager') and preg_match('/^[0-9]+$/',$_GET['id'])) {
	if ($_POST['confirm'] == 'true') {
		product_delete($_GET['id']);
		redirect('?module=products');
	} else {
		Module::template('products','delete.tpl');
	}
} else {
	redirect();
}
?>
