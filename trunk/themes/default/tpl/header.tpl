<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- Begin header.tpl -->
<html>
	<head>
		<title>{$title}</title>
		<link rel="stylesheet" type="text/css" media="print" href="themes/default/print.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="themes/default/screen.css" />
<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="themes/default/ie.css" />
<![endif]-->
{include file="javascript.tpl"}
	</head>
	<body>
		<table cellspacing="0" cellpadding="0" border="0" align="center" class="borders">
			<tr>
				<td id="header">
					<img src="{$smarty.env.imgs.logo}" width="200" height="48" alt="Issue Tracker" />
				</td>
			</tr>
			<tr><td class="menu">{if !empty($smarty.session.userid)}{navigation}{/if}</td></tr>
			<tr>
				<td id="content">
<!-- End header.tpl -->
