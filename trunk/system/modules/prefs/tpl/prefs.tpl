<!-- Begin prefs/prefs.tpl -->
		<form method="post" action="?module=prefs&update=true">
			<fieldset>
				<legend>Preferences</legend>
				<div class="label">First Name:</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="first" value="{$user.first_name}" /></div>
				<div class="label">Last Name:</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="last" value="{$user.last_name}" /></div>
				<div class="label">Address:</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="address" value="{$user.address}" /></div>
				<div class="label">&nbsp;</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="address2" value="{$user.address2}" /></div>
				<div class="label">Phone Number:</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="phone" value="{$user.telephone}" /></div>
				<div class="label">Email:</div>
				<div class="data"><input type="text" size="32" maxlength="32" name="email" value="{$user.email}" /></div>
				<div class="label">Show Fields:</div>
				<div class="data">
					<table width="100%" cellpadding="2" cellspacing="0">
{php}$col = 0;{/php}
{foreach from=$issue_fields item=field}
{php}if ($col == 0) { print "<tr>\n"; }{/php}
							<td width="20%"><input type="checkbox" name="fields[]" value="{$field.field}"{if in_array($field.field,$smarty.session.prefs.show_fields)} checked="checked"{/if} />{$field.name}</td>
{php}$col++; if ($col == 5) { print "</tr>\n"; $col = 0; }{/php}
{/foreach}
					</table>
				</div>
			</fieldset>
		</form>
<!--
{titlebar colspan=2 title="Change Password"}
<tr>
<td class="label" width="20%" align="right" valign="top">Old Password:</td>
<td class="data" width="80%"><input type="password" size="32" maxlength="32" name="oldpass" /></td>
</tr>
<tr>
<td class="label" width="20%" align="right" valign="top">New Password:</td>
<td class="data" width="80%"><input type="password" size="32" maxlength="32" name="newpass" /></td>
</tr>
<tr>
<td class="label" width="20%" align="right" valign="top">Confirm Password:</td>
<td class="data" width="80%"><input type="password" size="32" maxlength="32" name="confirm" /></td>
</tr>
<tr>
<td class="label" width="20%" align="right" valign="top">Use Local Timezone:</td>
<td class="data" width="80%"><input type="checkbox" name="localtz"{if $smarty.session.prefs.local_tz eq "t"} checked="checked"{/if} /></td>
</tr>
<tr>
<td class="label" width="20%" align="right" valign="top">Session Timeout Warnings:</td>
<td class="data" width="80%"><input type="checkbox" name="sesstimeout"{if $smarty.session.prefs.session_timeout eq "t"} checked="checked"{/if} /></td>
</tr>
{titlebar colspan=2 title="Issue Listing"}
<tr class="data">
<td class="label" width="20%" align="right" valign="top">Show Fields:</td>
<td class="data" width="80%">
</td>
</tr>
<tr>
<td class="label" width="20%" align="right" valign="top">Sort By:</td>
<td class="data" width="80%">
<select name="sort_by">
{foreach from=$issue_fields item=field}
{if $field.field ne "flags"}
<option value="{$field.field}"{if $field.field eq $smarty.session.prefs.sort_by} selected="selected"{/if}>{$field.name}</option>
{/if}
{/foreach}
</select>
</td>
</tr>
<tr class="titlebar"><td colspan="2"><input type="submit" value="Update Preferences" /></td></tr>
{closetable}
</form>
-->
<!-- End prefs/prefs.tpl -->

