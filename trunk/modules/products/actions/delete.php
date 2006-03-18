<?php
Module::check()
if (Permission::check('product_manager') and is_numeric($_GET['id'])) {
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
