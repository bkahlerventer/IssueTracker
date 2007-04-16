<?php
Module::check();
if (!preg_match('/^[0-9]+$/',$_GET['issueid'])) {
	redirect();
}

if (!empty($_GET['private']) and !empty($_GET['fid'])
and issue_priv($_GET['issueid'],'view_private')) {
	$sql = "SELECT typeid FROM files WHERE fid='".$_GET['fid']."' 
			AND file_type='issues' ";
	$id = $_ENV['dbi']->fetch_one($sql);
	if ($id == $_GET['issueid']) {
		$update['private'] = $_GET['private'] == 'true' ? 't' : 'f';
		$_ENV['dbi']->update('files',$update,"WHERE fid='".$_GET['fid']."'");
	}
}
if ($_GET['submit'] == 'true') {
	upload($_GET['issueid']);
	redirect("?module=issues&action=files&issueid=".$_GET['issueid']);
}
if (can_view_issue($_GET['issueid'])) {
	$links[] = array(
		'txt' => 'Back to Issue',
		'url' => '?module=issues&action=view&issueid='.$_GET['issueid'],
		'img' => $_ENV['imgs']['back']
	);
	$sql = "SELECT fid,userid,uploaded_on,name,private FROM files 
			WHERE typeid='".$_GET['issueid']."' AND file_type='issues' ";
	$sql .= !issue_priv($_GET['issueid'],'view_private') ? "AND private != 't' " : '';
	$sql .= "ORDER BY uploaded_on";
	$files = $_ENV['dbi']->fetch_all($sql,'array'); 
	$_ENV['tpl']->assign('files',$files);
	Module::template('issues','files.tpl');
} else {
	redirect();
}
?>
