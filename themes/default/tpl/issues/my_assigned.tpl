<!-- Begin issues/my_assigned.tpl -->
{opentable}
{titlebar colspan=6 title="My Issues (Assigned)"}
<tr class="tablehead" align="center">
<td width="5%"><a href="{$url}&sort=issueid">Id</a></td>
<td width="5%"><a href="{$url}&sort=severity">Sev</a></td>
<td width="15%"><a href="{$url}&sort=gid">Group</a></td>
<td align="left">Summary</td>
<td width="10%"><a href="{$url}&sort=opened_by">Opened By</a></td>
<td width="15%"><a href="{$url}&sort=status">Status</a></td>
</tr>
{if is_array($issues)}
{foreach from=$issues item=issue}
{if show_issue($issue.issueid,$issue.gid)}
<tr class="{rowcolor}" align="center">
<td width="5%">{$issue.issueid}</td>
<td width="5%"><img src="{sevimg sev=$issue.severity}" width="16" height="16" border="0" />({$issue.severity})</td>
<td width="15%">{groupname id=$issue.gid}</td>
<td align="left"><a href="?module=issues&action=view&issueid={$issue.issueid}">{$issue.summary|stripslashes}</a></td>
<td width="10%">{username id=$issue.opened_by}</td>
<td width="15%">{status id=$issue.status}</td>
</tr>
{/if}
{/foreach}
{else}
<tr class="data" align="center"><td colspan="6">No issues to display.</td></tr>
{/if}
{closetable}
<!-- End issues/my_assigned.tpl -->

