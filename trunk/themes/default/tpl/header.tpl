<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- Begin header.tpl -->
<html>
	<head>
		<title>{$title}</title>
		<link rel="stylesheet" type="text/css" media="print" href="themes/default/print.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="themes/default/screen.css" />
{if preg_match('/MSIE/',$smarty.server.HTTP_USER_AGENT)}
		<link rel="stylesheet" type="text/css" href="themes/default/ie.css" />
{/if}
{include file="javascript.tpl"}
	</head>
	<body>
		<div id="header">
			<img src="{$smarty.env.imgs.logo}" width="200" height="48" alt="Issue Tracker" />
{if !empty($smarty.session.userid)}
			<div id="crumbbar">
				<div id="crumbs">.: {$crumbs}</div>
			</div>
{/if}
		</div>
<!-- End header.tpl -->
