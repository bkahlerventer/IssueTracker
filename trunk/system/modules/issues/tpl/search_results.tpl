<!-- Begin issues/search_results.tpl -->
{opentable}
{titlebar colspan=4 title="Search Results"}
<tr class="tablehead" align="center">
<td width="5%">Id</td>
<td width="15%">Group</td>
<td align="left">Summary</td>
</tr>
{if is_array($issues)}
{foreach from=$issues item=issue}
<tr class="{rowcolor}" align="center">
<td width="5%">{$issue.issueid}</td>
<td width="15%">{groupname id=$issue.gid}</td>
<td align="left"><a href="?module=issues&action=view&issueid={$issue.issueid}">{$issue.summary|stripslashes}</a></td>
</tr>
{/foreach}
{else}
<tr class="data" align="center"><td colspan="3">Search returned no results.</td></tr>
{/if}
{closetable}
<!-- End issues/search_results.tpl -->

