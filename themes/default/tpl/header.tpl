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
		<script type="text/javascript">
{literal}
			function toggle_display(id) {
				if (document.layers) {
					current = (document.layers[id].display == 'none') ? 'block' : 'none';
					document.layers[id].display = current;
				} else if (document.all) {
					current = (document.all[id].style.display == 'none') ? 'block' : 'none';
					document.all[id].style.display = current;
				} else if (document.getElementById) {
					vista = (document.getElementById(id).style.display == 'none') ? 'block' : 'none';
					document.getElementById(id).style.display = vista;
				}
			}
{/literal}
{if !empty($smarty.session.userid)}
{if $smarty.session.prefs.local_tz eq 't'}
			var d = new Date();
			if (d.getTimezoneOffset) {ldelim}
				var iMinutes = d.getTimezoneOffset()
				document.cookie = "tz="+(iMinutes / 60)
			{rdelim}
{/if}
{/if}
		</script>
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
