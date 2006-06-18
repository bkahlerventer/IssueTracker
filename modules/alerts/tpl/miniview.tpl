<!-- Begin alerts/miniview.tpl -->
{if is_array($system) and $system.posted > (time() - _WEEK_)}
		<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" class="borders">
			<tr><td class="titlebar" style="background:red !important;">System Alert</td></tr>
			<tr><td>{$system.title} posted by {$system.username} on {$system.posted|userdate:TRUE}</td></tr>
			<tr>
				<td>
					{$system.message|format}
					<br />
					<a href="?module=alerts">[Other Alerts]</a>
				</td>
			</tr>
		</table>
		<br />
{/if}
		<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" class="borders">
			<tr><td class="titlebar">Group Alerts</td></tr>
{if count($alerts) > 0}
{foreach from=$alerts item=a}
			<tr><td><a href="?module=alerts&action=view&aid={$a.aid}">{$a.title}</a></td></tr>
{/foreach}
			<tr><td><a href="?module=alerts">More Alerts...</a></td></tr>
{else}
			<tr><td align="center">No Group Alerts at this time.</td></tr>
{/if}
		</table>
		<br />
<!-- End alerts/miniview.tpl -->

