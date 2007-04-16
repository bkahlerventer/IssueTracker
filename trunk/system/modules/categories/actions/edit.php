<?php
/**
* Edit an existing category and save the changes to the database
*/
module_check();
if (permission_check('category_manager')) {
	if (is_numeric($_GET['id'])) {
		if ($_POST['commit'] == 'true') {
			if (empty($_POST['category'])) {
				push_error('Please enter a valid name for the category.');
			} else {
				$sql = "SELECT cid FROM categories 
						WHERE LOWER(category) = LOWER('".trim($_POST['category'])."')";
				$cid = $_ENV['dbi']->fetch_one($sql);
				if (empty($cid) or $cid == $_GET['id']) {
					$update['category'] = $_POST['category'];
					$_ENV['dbi']->update('categories',$update,"WHERE cid='".$_GET['id']."'");
					redirect('?module=categories');
				} else {
					push_error('A category by that name already exists.');
				}
			}
		} 
		if (empty($_POST['commit']) or errors()) {
			$category = category($_GET['id']);
			$smarty->assign('category',$category);
			module_template('categories','edit.tpl');
		}	
	}
} else {
	redirect();
}
?>
