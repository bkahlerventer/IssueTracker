<?php
/* $Id: my_assigned.issues.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
 * @package Issue-Tracker
 * @subpackage Issues
 */

if (eregi(basename(__FILE__),$_SERVER['PHP_SELF'])) {
  print "Direct module access forbidden.";
  exit;
}

if($_GET['showall'] != "true"){
  $links[] = array(
    "img" => $_ENV['imgs']['show_closed'],
    "txt" => " Show Closed ",
    "url" => "?module=issues&action=my_assigned&showall=true&gid=".$_GET['gid']
  );
} else {
  $links[] = array(
    "img" => $_ENV['imgs']['hide_closed'],
    "txt" => " Hide Closed ",
    "url" => "?module=issues&action=my_assigned&showall=false&gid=".$_GET['gid']
  );
}

// retrieve needed statuses
list($registered) = fetch_status(TYPE_REGISTERED);
$closed = implode(",",fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));

// Make sure we have something to sort by
if (empty($_GET['sort'])) {
  $_GET['sort'] = "status";
}

$url  = "?module=issues&action=my_assigned$show$reverse";
$url .= $_GET['showall'] != "true" ? "&showall=true" : "";
$url .= $_GET['reverse'] != "true" ? "&reverse=true" : "";
$smarty->assign('url',$url);

// now show the rest of the issues
$sql  = "SELECT i.issueid,i.summary,i.opened_by,i.status,g.gid,i.severity ";
$sql .= "FROM issues i,issue_groups g";
$sql .= $_GET['sort'] == "opened_by" ? ",users u " : " ";
$sql .= "WHERE g.assigned_to='".$_SESSION['userid']."' ";
$sql .= "AND i.status != '$registered' ";
$sql .= $_GET['showall'] != "true" ? "AND i.status NOT IN ($closed) " : "";
$sql .= "AND i.issueid=g.issueid ";

switch ($_GET['sort']) {
	case "opened_by":
		$sql .= "AND u.userid=t.".$_GET['sort']." DESC ";
		$sql .= "ORDER BY u.username ";
		break;
	default:
		$sql .= "ORDER BY i.".$_GET['sort']." DESC ";
		break;
}

$issues = $dbi->fetch_all($sql,"array");

$smarty->assign('issues',$issues);
$smarty->display("issues/my_assigned.tpl");
?>
