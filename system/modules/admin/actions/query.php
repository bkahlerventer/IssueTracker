<?php
/* $Id: query.admin.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
 * @package Issue-Tracker
 * @subpackage Administration
 */

if (eregi(basename(__FILE__),$_SERVER['PHP_SELF'])) {
  print "Direct module access forbidden.";
	exit;
}

$disallow_tables = array(
  "sessions",
  "users"
);

if (eregi("explain|update|delete|drop|alter|cluster|vacuum",$_POST['query'])) {
	push_error("I DONT THINK SO!");
  logger(username($_SESSION['userid'])." is trying to do naughty things with database queries. (".$_POST['query'].")","alerts");
  unset($_POST['query']);
}

if (!empty($_POST['query']) and !ereg(";$",$_POST['query'])) {
  push_error("You must end your queries with a semi-colon (;).");
  unset($_POST['query']);
}

if (!empty($_POST['query'])) {
  if (!eregi("where",$_POST['query'])) {
    eregi("from (.+);",$_POST['query'],$matches);
  } else {
    eregi("from (.+)where",$_POST['query'],$matches);
  } 
  if (is_array($matches)) {
    $tables = $matches[1];
    $tables = split(",",$tables);
    foreach ($tables as $key => $val) {
      $table = split(" ",trim($val));
      $table = trim($table[0]);
      if (in_array($table,$disallow_tables)
      and !is_admin()) {
        push_error("Access to $table table denied.");
        unset($_POST['query']);
      }
    }
  }
}

if (!errors() and !empty($_POST['query'])) {
	$result = $dbi->query($_POST['query']);

  if ($dbi->num_rows($result) > 0) {
    if ($_POST['explain'] == "on") {
      $sql = "EXPLAIN ANALYZE ".$_POST['query'];
      $result2 = $dbi->query($sql);
      if ($dbi->num_rows($result2) > 0) {
        while (list($data) = $dbi->fetch($result2)) {
          $explain .= $data."\n";
        }
        $smarty->assign('explain',$explain);
      }
    }
  } 
}

if (!empty($_POST['query'])) {
	if ($dbi->num_rows($result) > 0) {
		$num_fields = $dbi->num_fields($result);

    $results = "<tr class=\"tablehead\">\n";

		for ($x = 0;$x < $num_fields;$x++) {
      $results .= "<td>".$dbi->field_name($result,$x)."</td>\n";
		}

    $results .= "</tr>\n";

    while ($data = $dbi->fetch($result)) {
      $results .= "<tr class=\"".rowcolor()."\">\n"; 

      for ($x = 0;$x < $num_fields;$x++) {
        $results .= "<td>".$data[$x]."</td>\n";
      }

      $results .= "</tr>\n";
    }
    $smarty->assign('results',$results);
	}
}

$links[] = array(
  "txt" => "Back to Administration",
  "url" => "?module=admin",
  "img" => $_ENV['imgs']['back']
);

$smarty->display("admin/query.tpl");
?>
