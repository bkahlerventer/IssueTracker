<!-- Begin alerts/miniview.tpl -->
{if is_array($system) and $system.posted > (time() - _WEEK_)}
		<fieldset>
			<legend style="background:red !important;">System Alert</legend>
			<label>{$system.title} posted by {$system.username} on {$system.posted|userdate:TRUE}</label><br />
			{$system.message|format}
			<br />
			<a href="?module=alerts">[Other Alerts]</a>
		</fieldset>
		<br />
{/if}
		<fieldset>
			<legend>Group Alerts</legend>
{if count($alerts) > 0}
{foreach from=$alerts item=a}
			<li><a href="?module=alerts&action=view&aid={$a.aid}">{$a.title}</a></li>
{/foreach}
			<br />
			<a href="?module=alerts">More Alerts...</a>
{else}
			<div align="center">No Group Alerts at this time.</div>
{/if}
		</fieldset>
		<br />
<!-- End alerts/miniview.tpl -->

