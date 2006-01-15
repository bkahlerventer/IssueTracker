<?php
/* $Id: new.issues.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
 * @package Issue-Tracker
 * @subpackage Issues
 */

if (eregi(basename(__FILE__),$_SERVER['PHP_SELF'])) {
  print "Direct module access forbidden.";
  exit;
}

if (empty($_GET['gid']) and !empty($_POST['gid'])) {
  $_GET['gid'] = $_POST['gid'];
}

if ($_POST['create']) {
  if (empty($_POST['summary'])) {
    push_error("Please enter a summary.");
  } else if (empty($_POST['severity'])) {
    push_error("Please select a severity.");
  } else if (empty($_POST['problem'])) {
    push_error("Please enter a problem.");
  } else if (empty($_POST['product'])) {
    push_error("Please choose a product.");
  } else if (!empty($_POST['duedate'])
  and !ereg("^(0{0,1}[1-9]|1[012])/(0{0,1}[1-9]|[12][0-9]|3[01])/(19|20)[0-9][0-9]$",$_POST['duedate'])) {
    push_error("Invalid Due Date.");
  }

  if (!errors()) {
    list($registered) = fetch_status(TYPE_REGISTERED);
    
    $insert['summary']  = str_replace("\"","'",$_POST['summary']);
    $insert['problem']  = $_POST['problem'];
    $insert['opened']   = time();
    $insert['modified'] = time();
    $insert['gid']      = $_GET['gid'];
    $insert['status']   = $registered;
    $insert['opened_by']= $_SESSION['userid'];
    $insert['severity'] = $_POST['severity'];
    $insert['product'] 	= $_POST['product'];
    $insert['private']  = $_POST['private'] == "on" ? "t" : "f";
    if (!empty($_POST['duedate'])) {
      $parts = split("/",$_POST['duedate']);
      $duedate = mktime(0,0,0,$parts[0],$parts[1],$parts[2]);
      $insert['due_date'] = $duedate;
    }
    $issueid = $dbi->insert("issues",$insert,"issues_issueid_seq");
    if (!is_null($issueid)) {
      issue_log($issueid,"Issue Registered");
      unset($insert);

      $insert['issueid']= $issueid;
      $insert['gid']		= $_GET['gid'];
      $insert['opened']	= time();
      $dbi->insert("issue_groups",$insert);
      unset($insert);
      
      if (!empty($_FILES['upload']['name'])) {
        upload($issueid);
      }
      
      $subject = "(NEW) Issue #$issueid";
      $message  = "New issue posted by ".username($_SESSION['userid'])."\n\n";
      $message .= "Summary: ".str_replace("\"","'",$_POST['summary']);
      $message .= "\n\n"._URL_."?module=issues&action=view&issueid=$issueid&gid=".$_GET['gid'];
      issue_notify($issueid,$_POST['notify']);
      redirect("?module=issues&action=view&issueid=$issueid&gid=".$_GET['gid']);
    } else {
      push_fatal_error("This issue could not be created.");
    }
  }
}

if (empty($_GET['gid'])) {
  if ($_SESSION['group_count'] > 1) {
    $smarty->display("issues/choose_new.tpl");
  } else {
    $gid = $_SESSION['groups'][0];
    redirect("?module=issues&action=new&gid=$gid");
  }
} else {
  if (permission_check("create_issues",$_GET['gid'])) {
    if (!empty($_GET['icopy'])) {
      if (can_view_issue($_GET['icopy'])) {
        $sql  = "SELECT summary,problem ";
        $sql .= "FROM issues ";
        $sql .= "WHERE issueid='".$_GET['icopy']."'";
        list($summary,$problem) = $dbi->fetch_row($sql);
        if (!empty($summary)) {
          $_POST['summary'] = $summary;
          $_POST['problem'] = $problem;
        }
      }
    }
  
    $smarty->assign('products',group_products($_GET['gid']));
    $smarty->assign('members',group_members($_GET['gid']));
    $smarty->display("issues/new.tpl");
  } else {
    redirect();
  }
}
?>
