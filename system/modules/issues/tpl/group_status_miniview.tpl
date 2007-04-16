<!-- Begin issues/group_status_miniview.tpl -->
		<fieldset>
			<legend>Group Standing</legend>
			<table width="100%" align="center" border="0">
				<tr>
					<th width="5%" align="center">Standing</th>
					<th>Group</th>
					<th width="5%" align="center">New</th>
					<th width="5%" align="center">Open</th>
					<th width="5%" align="center">Urgent</th>
					<th width="5%" align="center">High</th>
					<th width="5%" align="center">Normal</th>
					<th width="5%" align="center">Low</th>
				</tr>
{if count($group_status) > 0}
{foreach from=$group_status key=gid item=group}
{if show_group($gid)}
				<tr class="{rowcolor}">
					<td width="5%" align="center"><img src="{$group.standing}" width="16" height="16" border="0" /></td>
					<td><a href="?module=issues&action=group&gid={$gid}">{$group.name}</a></td>
					<td width="5%" align="center">{$group.new}</td>
					<td width="5%" align="center">{$group.open}</td>
					<td width="5%" align="center">{$group.sev1}</td>
					<td width="5%" align="center">{$group.sev2}</td>
					<td width="5%" align="center">{$group.sev3}</td>
					<td width="5%" align="center">{$group.sev4}</td>
				</tr>
{/if}
{/foreach}
{else}
				<tr class="data"><td colspan="8" align="center">No group standings to display.</td></tr>
{/if}
			</table>
			<div style="text-align: center; margin: 2px;">
				<img src="{$smarty.env.imgs.normal}" width="16" height="16" border="0" alt="Rating below 25" /> = Good
				<img src="{$smarty.env.imgs.high}" width="16" height="16" border="0" alt="Rating between 25 and 49" /> = Fair
				<img src="{$smarty.env.imgs.urgent}" width="16" height="16" border="0" alt="Rating of 50 or higher" /> = Critical
			</div>
		</fieldset>
		<br />
<!-- End issues/group_status_miniview.tpl-->
