<!-- Begin login.tpl -->
{if is_array($smarty.session.errors)}
{if count($smarty.session.errors) > 0}
{include file="errors.tpl"}
{/if}
{/if}
{if $smarty.get.forgotten_password eq "true"}
<form method="post" action="?module=public&action=forgotten_password&send=true&forgotten_password=true">
<table width="98%" align="center" border="0" cellpadding="2" cellspacing="0" bgcolor="#cccccc" style="border: 1px solid black;">
{titlebar colspan=2 title="Forgotten Password Form"}
<tr>
<td width="60%" align="right" valign="top">Username:</td>
<td width="40%"><input type="text" size="16" name="username" /></td>
</tr>
<tr>
<td width="60%" align="right" valign="top">Email:</td>
<td width="40%"><input type="text" size="16" name="email" /></td>
</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Send Password" /></td></tr>
</table>
</form>
<br />
</td>
<td width="80%" style="margin: 4px; padding: 4px;"><br />{$motd}</td>
{elseif $smarty.get.register eq "true" and $allow_register eq TRUE}
<form method="post" action="?module=public&action=register&create=true&register=true">
<table width="98%" align="center" class="login" border="0" cellpadding="2" cellspacing="0">
{titlebar colspan=2 title="Account Registration"}
<tr>
<td width="60%" align="right" valign="top">Username:</td>
<td width="40%"><input type="text" size="16" name="username" value="{$smarty.post.username}" /></td>
</tr>
<tr>
<td width="60%" align="right" valign="top">Email:</td>
<td width="40%"><input type="text" size="16" name="email" value="{$smarty.post.email}" /></td>
</tr>
<tr>
<td width="60%" align="right" valign="top">First Name:</td>
<td width="40%"><input type="text" size="16" name="firstname" value="{$smarty.post.firstname}" /></td>
</tr>
<tr>
<td width="60%" align="right" valign="top">Last Name:</td>
<td width="40%"><input type="text" size="16" name="lastname" value="{$smarty.post.lastname}" /></td>
</tr>
<tr><td colspan="2" align="center"><input type="submit" value="Create Account" /></td></tr>
</table>
</form>
<br />
</td>
<td width="80%" style="margin: 4px; padding: 4px;"><br />{$motd}</td>
{else}
<table cellspacing="0" cellpadding="0" border="0" align="center" class="borders">
	<tr><td class="titlebar" colspan="2">Issue-Tracker Login</td></tr>
	<tr>
		<td align="right" width="150">
			<form method="post" name="loginForm" action="{$smarty.const._URL_}">
				<input type="hidden" name="request" value="{$smarty.server.QUERY_STRING}" />
				<script language="JavaScript" type="text/javascript">
					document.write('<input type="hidden" name="javascript" value="enabled">');
				</script>
				<label>Username:</label>&nbsp;<input type="text" size="16" name="username" /><br />
				<label>Password:</label>&nbsp;<input type="password" size="16" name="password" /><br />
				<input type="submit" value="Login" />
			</form>
		</td>
		<td>{$motd}</td>
	</tr>
</table>
{/if}
<!-- End login.tpl -->

