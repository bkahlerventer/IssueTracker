<?php
Module::check();
// if the user belongs to multiple groups make them select
// which groups issues they want to view
if ($_SESSION['group_count'] > 1 or Permission::is_manager()) {
	redirect('?module=issues&action=choose');
} else {
	// otherwise assign gid as the only group and include
	// the group issues file
	$gid = $_SESSION['groups'][0];
	redirect("?module=issues&action=group&gid=$gid");
}
?>
