<!-- Begin alerts/miniview.tpl -->
{if is_array($system) and $system.posted > (time() - _WEEK_)}
{opentable}
<tr><td class="titlebar">System Alert</td></tr>
<tr><td class="label">{$system.title} posted by {$system.username} on {$system.posted|userdate:TRUE}</td></tr>
<tr><td class="data">{$system.message|format}</td></tr>
<tr><td class="label"><a href="?module=alerts">[Other Alerts]</a></td></tr>
{closetable}
<br />
{/if}
{opentable}
<tr><td class="titlebar">Group Alerts</td></tr>
{if count($alerts) > 0}
<tr>
<td class="data">
{foreach from=$alerts item=a}
<li><a href="?module=alerts&action=view&aid={$a.aid}">{$a.title}</a></li>
{/foreach}
<br />
<a href="?module=alerts">More Alerts...</a>
</td>
</tr>
{else}
<tr><td class="data" align="center">No Group Alerts at this time.</td></tr>
{/if}
{closetable}
<br />
<!-- End alerts/miniview.tpl -->

