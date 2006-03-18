<!-- Begin issues/miniview.tpl -->
		<fieldset>
			<legend>Last Updated</legend>
			<table width="100%" align="center" border="0">
				<tr align="center">
					<th width="5%">Id</th>
					<th width="15%">Group</th>
					<th align="left">Summary</th>
					<th width="10%">Updated</th>
					<th width="15%">Status</th>
				</tr>
{if is_array($last_issues)}
{foreach from=$last_issues item=issue}
				<tr class="{rowcolor}" align="center">
					<td width="5%">{$issue.issueid}</td>
					<td width="15%">{groupname id=$issue.gid}</td>
					<td align="left"><a href="?module=issues&action=view&issueid={$issue.issueid}">{$issue.summary|stripslashes}</a></td>
					<td width="10%">{$issue.modified|userdate}</td>
					<td width="15%">{status id=$issue.status}</td>
				</tr>
{/foreach}
{else}
				<tr class="data"><td colspan="5" align="center">No issues to list.</td></tr>
{/if}
			</table>
		</fieldset>
		<br />
		<fieldset>
			<legend>My Opened</legend>
			<table width="100%" align="center" border="0">
				<tr align="center">
					<th width="5%">Id</th>
					<th width="15%">Group</th>
					<th align="left">Summary</th>
					<th width="10%">Updated</th>
					<th width="15%">Status</th>
				</tr>
{if is_array($opened_issues)}
{foreach from=$opened_issues item=issue}
				<tr class="{rowcolor}" align="center">
					<td width="5%">{$issue.issueid}</td>
					<td width="15%">{groupname id=$issue.gid}</td>
					<td align="left"><a href="?module=issues&action=view&issueid={$issue.issueid}">{$issue.summary|stripslashes}</a></td>
					<td width="10%">{$issue.modified|userdate}</td>
					<td width="15%">{status id=$issue.status}</td>
				</tr>
{/foreach}
{else}
				<tr class="data"><td colspan="5" align="center">No issues to list.</td></tr>
{/if}
			</table>
		</fieldset>
		<br />
		<fieldset>
			<legend>My Assigned</legend>
			<table width="100%" align="center" border="0">
				<tr align="center">
					<th width="5%">Id</th>
					<th width="15%">Group</th>
					<th align="left">Summary</th>
					<th width="10%">Updated</th>
					<th width="15%">Status</th>
				</tr>
{if is_array($assigned_issues)}
{foreach from=$assigned_issues item=issue}
				<tr class="{rowcolor}" align="center">
					<td width="5%">{$issue.issueid}</td>
					<td width="15%">{groupname id=$issue.gid}</td>
					<td align="left"><a href="?module=issues&action=view&issueid={$issue.issueid}">{$issue.summary|stripslashes}</a></td>
					<td width="10%">{$issue.modified|userdate}</td>
					<td width="15%">{status id=$issue.status}</td>
				</tr>
{/foreach}
{else}
				<tr class="data"><td colspan="5" align="center">No issues to list.</td></tr>
{/if}
			</table>
		</fieldset>
<!-- End issues/miniview.tpl -->

