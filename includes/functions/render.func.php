<?php
if (defined("BROWSER")) {
	# Register template modifiers
	$_ENV['tpl']->register_modifier('number_format','number_format');
	$_ENV['tpl']->register_modifier('format','template_text_format');
	$_ENV['tpl']->register_modifier('userdate','template_date_format');

	# Register block functions
	$_ENV['tpl']->register_block('container','container');
	$_ENV['tpl']->register_block('subcontainer','subcontainer');

	# Register template functions
	$_ENV['tpl']->register_function('opennavtable','template_opennavtable');
	$_ENV['tpl']->register_function('closenavtable','template_closenavtable');
	$_ENV['tpl']->register_function('opentable','template_opentable');
	$_ENV['tpl']->register_function('closetable','template_closetable');
	$_ENV['tpl']->register_function('titlebar','template_titlebar');
	$_ENV['tpl']->register_function('username','template_username');
	$_ENV['tpl']->register_function('groupname','template_group_name');
	$_ENV['tpl']->register_function('rowcolor','template_rowcolor');
	$_ENV['tpl']->register_function('alphalist','template_alpha_list');
	$_ENV['tpl']->register_function('sevimg','template_severity_image');
	$_ENV['tpl']->register_function('sevtxt','template_severity_text');
	$_ENV['tpl']->register_function('category','template_category');
	$_ENV['tpl']->register_function('product','template_product');
	$_ENV['tpl']->register_function('status','template_status');
	$_ENV['tpl']->register_function('fsize','template_fsize');
	$_ENV['tpl']->register_function('subtitle','template_subtitle');
	$_ENV['tpl']->register_function('date_select','template_date_select');
	$_ENV['tpl']->register_function('navigation','template_navigation');

	/** Create a content container */
	function container($params,$content,&$smarty,&$repeat) {
		$smarty->assign('title',$params['title']);
		$smarty->display('open_container.tpl');
		echo($content);
		$smarty->display('close_container.tpl');
	}


	/** Open navigation table */
	function template_opennavtable($params,&$smarty) {
		if (!$_SESSION['userid']) {
			if (count($_SESSION['errors']) > 0) {
				$smarty->display("errors.tpl");
			}
		}
		$smarty->display("opennavtable.tpl");
	}

  /**
   * Close navigation table
   */
  function template_closenavtable($params,&$smarty)
  {
    $smarty->display("closenavtable.tpl");
  }

  /**
   * Open content table
   */
  function template_opentable($params,&$smarty)
  {
    if (count($_SESSION['errors']) > 0) {
      $smarty->display("errors.tpl");
    }
  
    $smarty->display("opentable.tpl");
  }

  /**
   * Close content table
   */
  function template_closetable($params,&$smarty)
  {
    $smarty->display("closetable.tpl");
  }

  /**
   * Create titlebar row and possible linkbar row
   *
   * @param integer $colspan Number of columns titlebar should take
   * @param string $title Title to put in titlebar
   */
  function template_titlebar($params,&$smarty)
  {
    global $links;

    extract($params);

    $colspan = empty($colspan) ? 1 : $colspan;
    $title = empty($title) ? "&nbsp;" : $title;

    $buffer = "<tr><td class=\"titlebar\" colspan=\"$colspan\">$title</td></tr>\n";

    if (is_array($links) and count($links) > 0) {
      $buffer .= '<tr><td class="subtitle" colspan="'.$colspan.'">';
      foreach ($links as $link) {
        $buffer .= '[&nbsp;';
        $buffer .= '<a href="'.$link['url'].'">';
        if (!empty($link['img'])) {
          $buffer .= '<img src="'.$link['img'].'" width="16" height="16" border="0" alt="'.$link['txt'].'" />&nbsp;';
        }
        $buffer .= $link['txt'];
        $buffer .= '</a>&nbsp;]';
      }
      $buffer .= '</td></tr>';
      $links = array();
    }

    return $buffer;
  }

  /**
   * Wrapper to username function for use in templates
   *
   * @param integer $id ID of user to return username for
   */
  function template_username($params,&$smarty)
  {
    extract($params);

    $username = username($id);
    return $username;
  }

  /**
   * Wrapper to group_name function for use in templates
   *
   * @param integer $id ID of group to return name for
   */
  function template_group_name($params,&$smarty)
  {
    extract($params);

    $name = group_name($id);
    return $name;
  }

  /**
   * Wrapper to date_format function for use in templates
   *
   * @param integer $time Unix Timestamp
   */
  function template_date_format($timestamp,$showtime = FALSE)
  {
    if ($showtime == TRUE) {
      $date = date_format($timestamp,TRUE);
    } else {
      $date = date_format($timestamp,FALSE);
    }
    return $date;
  }

  /**
   * Wrapper to rowcolor function for use in templates
   */
  function template_rowcolor($params,&$smarty)
  {
    $rowcolor = rowcolor();
    return $rowcolor;
  }

  /**
   * Builds list of links to each letter
   *
   * @param string $url
   */
  function template_alpha_list($params,&$smarty)
  {
    extract($params);
    
    $buffer  = "<a href=\"$url&start=ALL\">All</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=A\">A</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=B\">B</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=C\">C</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=D\">D</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=E\">E</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=F\">F</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=G\">G</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=H\">H</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=I\">I</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=J\">J</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=K\">K</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=L\">L</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=M\">M</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=N\">N</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=O\">O</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=P\">P</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=Q\">Q</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=R\">R</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=S\">S</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=T\">T</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=U\">U</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=V\">V</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=W\">W</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=X\">X</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=Y\">Y</a>&nbsp;\n";
    $buffer .= "<a href=\"$url&start=Z\">Z</a>&nbsp;\n";

    return $buffer;
  }

  /**
   * Wrapper to severity_image function for use in templates
   */
  function template_severity_image($params,&$smarty)
  {
    extract($params);
    return severity_image($sev);
  }
  
  /**
   * Wrapper to severity_text function for use in templates
   */
  function template_severity_text($params,&$smarty)
  {
    extract($params);
    return severity_text($sev);
  }

  /**
   * Wrapper to category function for use in templates
   */
  function template_category($params,&$smarty)
  {
    extract($params);
    return category($id);
  }

  /**
   * Wrapper to product function for use in templates
   */
  function template_product($params,&$smarty)
  {
    extract($params);
    return product($id);
  }

  /**
   * Wrapper to status function for use in templates
   */
  function template_status($params,&$smarty)
  {
    extract($params);
    return status($id);
  }

  /**
   * Format plain text for viewing in html
   *
   * @param string $text Text to format
   */
  function template_text_format($text)
  {
    // Remove Escape Character Slashes
    $text = trim(stripslashes($text));
    // Get rid of carriage returns
    $text = str_replace("\r","",$text);
    // Strip out all html except simple text formatting
    $text = htmlentities($text);
    // Convert urls to hyperlinks
    $text = eregi_replace("((http://)|(https://).[^\s]+)\ ","<a href=\"\\0\">\\0</a>",$text); 

    if ($_SESSION['prefs']['disable_wrap'] != "t") {
      $lines = explode("\n",$text);
      $text = "";

      $wrap = preg_match("/[0-9]+/",$_SESSION['prefs']['word_wrap']) ? $_SESSION['prefs']['word_wrap'] : 80;
      foreach ($lines as $key => $val) {
        if (empty($val)) {
          $text .= "\n";
        } else {
          if (strlen($val) > $wrap) {
            $val = wordwrap($val,$wrap,"\n",TRUE);
          }

          $text .= stripslashes($val)."\n";
        }
      }
      
      $text = "<pre>$text</pre>";
    } else {
      // Replace newlines with <br />'s
      $text = str_replace("\n","<br />",$text);
    }
    return $text;
  }

  /**
   * Wrapper to the fsize function for use in templates
   *
   * @param integer $id Id of file to check size of
   */
  function template_fsize($params,&$smarty)
  {
    extract($params);
    return fsize($id);
  }

  /**
   * Create subtitle bar
   *
   * @param string $text Text to show in subtitle
   * @param integer $colspan Number of columns to take up
   */
  function template_subtitle($params,&$smarty)
  {
    extract($params);
    
    $buffer = "<tr><td class=\"subtitle\" colspan=\"$colspan\">$title</td></tr>";
    return $buffer;
  }

  /**
   * Date selection field, uses javascript calendar if available
   *
   * @param string $name Field name, also used for id
   * @param integer $value Default value
   */
  function template_date_select($params,&$smarty)
  {
    extract($params);
    
    if (empty($name)) {
      return;
    }
    
    $buffer  = '<input type="text" size="12" maxlength="12" name="'.$name.'" value="'.$value.'" /> (Format: mm/dd/yyyy)';
    return $buffer;
  }

  /**
   * Text for Severities
   *
   * @param integer $severity Severity to display text for
   * @returns string
   */
  function severity_text($severity)
  {
    switch($severity){
      case 1:     $text = "Urgent";   break;
      case 2:     $text = "High";     break;
      case 3:     $text = "Normal";   break;
      default:    $text = "Low";      break;
    }

    return $text;
  }

  /**
   * Image for severities
   *
   * @param integer $severity Severity to show image for
   * @returns string
   */
  function severity_image($severity)
  {
    switch($severity){
      case 1:     $img = $_ENV['imgs']['urgent']; break;
      case 2:     $img = $_ENV['imgs']['high'];   break;
      case 3:     $img = $_ENV['imgs']['normal']; break;
      default:    $img = $_ENV['imgs']['low'];    break;
    }

    return $img;
  }

  /**
   * Alternates table row classes
   * 
   * @returns string $class
   */
  function rowcolor()
  {
    static $rowclass;

    if (empty($rowclass)) {
      $rowclass = 1;
    }

    if ($rowclass == 2) {
      $rowclass = 1;
      return "row2";
    } else {
      $rowclass = 2;
      return "row1";
    }
  }

  /**
   * Build breadcrumbs
   *
   * @param string $divider Character(s) to use as divider
   */
  function build_crumbs($divider = "::")
  {
    global $dbi;

    $divider = " $divider ";
    $crumbs = " <a href=\""._URL_."\">Main</a>";

    if (!empty($_GET['module'])) {
      $crumbs .= "$divider<a href=\"?module=".$_GET['module']."\">";
      $crumbs .= ucwords(str_replace("_"," ",$_GET['module']));
      $crumbs .= "</a>";
    }

    if (!empty($_GET['start'])) {
      $crumbs .= "<a href=\"?module=".$_GET['module']."&start=\"".$_GET['start']."\">";
      $crumbs .= "(".$_GET['start'].")";
      $crumbs .= "</a>";
    }

    if (!empty($_GET['type']) and empty($_GET['action'])) {
      $crumbs .= "$divider<a href=\"?module=".$_GET['module']."&type=".$_GET['type']."\">";
      $crumbs .= ucwords(str_replace("_"," ",$_GET['type']));
      $crumbs .= "</a>";
    }

    if (!empty($_GET['gid'])) {
      if ($_GET['module'] == "issues") {
        $crumbs .= "$divider<a href=\"?module=issues&action=group&gid=".$_GET['gid']."\">";
      } else {
        $crumbs .= "$divider<a href=\"?module=groups&action=view&gid=".$_GET['gid']."\">";
      }
      $crumbs .= group_name($_GET['gid']);
      $crumbs .= "</a>";
    }

    if (!empty($_GET['uid'])) {
      $crumbs .= "$divider<a href=\"?module=users&action=view&uid=".$_GET['uid']."\">";
      $crumbs .= username($_GET['uid'],FALSE);
      $crumbs .= "</a>";
    }

    if (!empty($_GET['issueid'])) {
      if (empty($_GET['gid'])) {
        $sql  = "SELECT gid ";
        $sql .= "FROM issues ";
        $sql .= "WHERE issueid='".$_GET['issueid']."'";
        $result = $dbi->query($sql);
        if ($dbi->num_rows($result) > 0) {
          list($gid) = $dbi->fetch($result);
          $crumbs .= "$divider<a href=\"?module=issues&action=group&gid=$gid\">";
          $crumbs .= group_name($gid);
          $crumbs .= "</a>";
        }
      }

      $crumbs .= "$divider<a href=\"?module=issues&action=view&issueid=".$_GET['issueid']."\">";
      $crumbs .= "Issue #".$_GET['issueid'];
      $crumbs .= "</a>";
    }

    if (!empty($_GET['action'])) {
      $uri = str_replace("/?module=".$_GET['module'],"",$_SERVER['REQUEST_URI']);
      $uri = str_replace("&action=".$_GET['action'],"",$uri);
      $action = ucwords(str_replace("_"," ",$_GET['action']));
      
      if (!empty($_GET['subaction'])) {
        $action .= " (".ucwords(str_replace("_"," ",$_GET['subaction'])).")";
      }

      if (!empty($_GET['type'])) {
        $action .= " (".ucwords(str_replace("_"," ",$_GET['type'])).")";
      }

      $url = "?module=".$_GET['module']."&action=".$_GET['action'];

      if (!empty($_GET['gid'])) {
        $url .= "&gid=".$_GET['gid'];
        $uri = str_replace("&gid=".$_GET['gid'],"",$uri);
      }

      if (!empty($_GET['uid'])) {
        $url .= "&uid=".$_GET['uid'];
        $uri = str_replace("&uid=".$_GET['uid'],"",$uri);
      }

      if (!empty($_GET['issueid'])) {
        $url .= "&issueid=".$_GET['issueid'];
        $uri = str_replace("&issueid=".$_GET['issueid'],"",$uri);
      }

      $crumbs .= "$divider<a href=\"$url\">$action</a>";
    }

    return $crumbs;
  }

	/**
	* Generate the menu array to be used for navigation
	*/
	function template_navigation() {
		$_ENV['menu'] = array();
		$_ENV['menu']['Main'] = _URL_;
		Module::includes('menus');
		$_ENV['menu']['Preferences'] = '?module=prefs';
		$_ENV['menu']['Logout'] = '?logout=true';

		$buffer = '<div id="menu"><ul><li><h2>';
		$buffer .= array_key_exists('Menu',$_ENV['imgs']['menu']) 
			? '<img src="'.$_ENV['imgs']['menu']['Menu'].'" border="0" /> ' : ' ';
		$buffer .= ' Main Menu</h2><ul>';
		foreach ($_ENV['menu'] as $module => $items) {
			if (is_array($items)) {
				$buffer .= '<li><a href="#">';
				$buffer .= array_key_exists($module,$_ENV['imgs']['menu'])
					? '<img src="'.$_ENV['imgs']['menu'][$module].'" border="0" /> ' : ' ';
				$buffer .= $module.'</a><ul>';
				foreach ($items as $label => $url) {
					$buffer .= '<li><a href="'.$url.'">';
					$buffer .= array_key_exists($label,$_ENV['imgs']['menu'])
						? '<img src="'.$_ENV['imgs']['menu'][$label].'" border="0" /> ' : ' ';
					$buffer .= $label.'</a></li>';
				}
				$buffer .= '</ul></li>';
			} else {
				$buffer .= '<li><a href="'.$items.'">';
				$buffer .= array_key_exists($module,$_ENV['imgs']['menu'])
					? '<img src="'.$_ENV['imgs']['menu'][$module].'" border="0" /> ' : ' ';
				$buffer .= $module.' </a></li>';
			}	
		}
		$buffer .= '</ul></li></ul></div>';
		return $buffer;
	}
}
?>
