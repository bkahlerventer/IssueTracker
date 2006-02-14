<!-- Begin alerts/alerts.tpl -->
{opentable}
{titlebar title="Alerts"}
<tr class="label"><td><a href="?module=alerts&action=group&gid=">System Alerts</a></td></tr>
{if is_array($system) and count($system) > 0}
{foreach from=$system key=aid item=title}
<tr class="data"><td><a href="?module=alerts&action=view&aid={$aid}">{$title}</a></td></tr>
{/foreach}
{else}
<tr class="data"><td>No system alerts at this time.</td></tr>
{/if}
{if is_array($alerts)}
{foreach from=$alerts key=gid item=a}
<tr class="label"><td><a href="?module=alerts&action=group&gid={$gid}">{groupname id=$gid}</a></td></tr>
{if count($a) > 0}
{foreach from=$a item=alert}
<tr class="data"><td><a href="?module=alerts&action=view&aid={$alert.aid}">{$alert.title}</a></td></tr>
{/foreach}
{else}
<tr class="data"><td>No alerts for this group.</td></tr>
{/if}
{/foreach}
{/if}
{closetable}
<!-- End alerts/alerts.tpl -->

