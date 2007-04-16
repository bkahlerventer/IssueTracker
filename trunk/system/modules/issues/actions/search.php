<?php
Module::check();
if ($_GET['advanced'] != 'true'){
	$links[] = array(
		'img' => $_ENV['imgs']['search'],
		'txt' => 'Advanced Search',
		'url' => '?module=issues&action=search&advanced=true'
	);
} else {
	$links[] = array(
		'img' => $_ENV['imgs']['search'],
		'txt' => 'Simple Search',
		'url' => '?module=issues&action=search'
	);
}

// Only show the other options if we are doing an advanced search
if ($_GET['advanced'] == 'true') {
	if (Permission::is_employee()) {
		$sql = "SELECT gid FROM groups ORDER BY name";
		$ugroups = $_ENV['dbi']->fetch_all($sql);

		$sql = "SELECT userid,username FROM users ORDER BY username";
		$u = $_ENV['dbi']->fetch_all($sql,'array');
		foreach ($u as $user) {
			$users[$user['userid']] = $user['username'];
		}

		$sql = "SELECT sid,status FROM statuses ORDER BY status";
		$s = $_ENV['dbi']->fetch_all($sql,'array');
		foreach ($s as $status) {
			$statuses[$status['sid']] = $status['status'];
		}
    
		$sql = "SELECT cid,category FROM categories ORDER BY category";
		$c = $_ENV['dbi']->fetch_all($sql,'array');
		foreach ($c as $category) {
			$categories[$category['cid']] = $category['category'];
		}
    
		$sql = "SELECT pid,product FROM products ORDER BY product";
		$p = $_ENV['dbi']->fetch_all($sql,'array');
		foreach ($p as $product) {
			$products[$product['pid']] = $product['product'];
		}
	} else {
		$ugroups = $_SESSION['groups'];
		foreach ($_SESSION['groups'] as $gid) {
			$members = group_members($gid);
			foreach ($members as $uid => $username) {
				if (!in_array($uid,$users)) {
					$users[$uid] = $username;
				}
			}
			$group_statuses = group_statuses($gid);
			foreach ($group_statuses as $sid => $status) {
				if (!array_key_exists($sid,$statuses)) {
					$statuses[$sid] = $status;
				}
			}
			$group_categories = group_categories($gid);
			foreach ($group_categories as $cid => $category) {
				if (!array_key_exists($cid,$categories)) {
					$categories[$cid] = $category;
				}
			}
			$group_products = group_products($gid);
			foreach ($group_products as $pid => $product) {
				if (!array_key_exists($pid,$products)) {
					$products[$pid] = $product;
				}
			}
		}
	}
	$_ENV['tpl']->assign('ugroups',$ugroups);
	$_ENV['tpl']->assign('users',$users);
	$_ENV['tpl']->assign('statuses',$statuses);
	$_ENV['tpl']->assign('categories',$categories);
	$_ENV['tpl']->assign('products',$products);
}
Module::template('issues','search.tpl');
?>
