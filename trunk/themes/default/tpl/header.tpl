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
			<div id="menu">
				<ul>
					<li><h2><img src="themes/default/images/png/menu.png" /> Main Menu</h2>
						<ul>
{foreach from=$smarty.env.menu key=module item=items}
{if is_array($items)}
							<li><a href="#">&raquo;&nbsp;{$module}</a>
							<ul>	
{foreach from=$items key=label item=url}
								<li><a href="{$url}">{$label}</a></li>
{/foreach}
							</ul>
{else}
							<li><a href="{$items}">&raquo;&nbsp;{$module}</a></li>
{/if}
{/foreach}
						</ul>
					</li>
				</ul>
			</div>
{/if}
		</div>
		<div id="content">
<!-- End header.tpl -->
