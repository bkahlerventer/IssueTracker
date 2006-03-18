<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- Begin header.tpl -->
<html>
<head>
<title>{$title}</title>
{if ereg("MSIE",$smarty.server.HTTP_USER_AGENT)}
<link rel="stylesheet" type="text/css" href="themes/default/ie.css" />
{/if}
{if $smarty.get.print eq "true"}
<link rel="stylesheet" type="text/css" href="css/print.css" />
{else}
<link rel="stylesheet" type="text/css" media="print" href="css/print.css" />
<link rel="stylesheet" type="text/css" media="screen" href="themes/default/screen.css" />
{/if}
{include file="javascript.tpl"}
</head>
{if !empty($smarty.session.userid)}
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" onLoad="loader(); return true;" onUnLoad="unloader(); return true;">
{else}
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">
{/if}
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="header" align="left" colspan="2"><img src="{$smarty.env.imgs.logo}" width="200" height="48" alt="Issue Tracker" /></td></tr>
{if !empty($smarty.session.userid)}
<tr>
{if ereg("(4.7)|(4.8)",$smarty.server.HTTP_USER_AGENT)}
<td class="crumb" width="50%">&nbsp;</td>
{else}
<td class="crumb" width="50%"> .:{$crumbs}</td>
{/if}
{if $smarty.get.module ne 'help'}
<td class="crumb" align="right" valign="top" width="50%">
<form method="post" action="?module=issues&action=view">
<input type="text" size="6" name="issueid" /><input type="submit" value="View Issue" />
</form>
</td>
{else}
<td class="crumb" align="right" width="50%">&nbsp;</td>
{/if}
</tr>
{/if}
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<!-- End header.tpl -->

