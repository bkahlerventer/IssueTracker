<!-- Begin alerts/group.tpl -->
{opentable}
{titlebar title=$title}
{if count($alerts) > 0}
{foreach from=$alerts key=aid item=title}
<tr class="data"><td><a href="?module=alerts&action=view&aid={$aid}">{$title}</a></td></tr>
{/foreach}
{else}
<tr class="data"><td>No alerts for this group.</td></tr>
{/if}
{closetable}
<br />
{include file="alerts/new.tpl"}
<!-- End alerts/group.tpl -->

