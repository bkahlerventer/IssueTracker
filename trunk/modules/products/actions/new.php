<?php
Module::check();
if (Permission::check('product_manager')) {
	if ($_POST['commit'] == 'true') {
		if (empty($_POST['product'])) {
			push_error('Please enter a valid name for the product.');
		} else {
			if (product_exists($_POST['product'])) {
				push_error('A product by that name already exists.');
			} else {
				product_create($_POST['product']);
				redirect('?module=products');
			}
		}
	}
	Module::template('products','new.tpl');
} else {
	redirect();
}
?>
