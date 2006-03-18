<!-- Begin leftnav.tpl -->
		<div id="navigation">
			<fieldset>
				<legend>Main Menu</legend>
{foreach from=$smarty.env.menu key=key item=val}
{if !preg_match('/^[0-9]+$/',$key)}
{if !is_array($val)}
				<div class="menu"><a href="{$val}">{$key}</a></div>
{elseif !empty($val.url)}
				<div class="menu"><a href="{$val.url}">{$key}</a></div>
{else}
				<div class="menu">{$key}</div
{/if}
{/if}
{if is_array($val.sub) and count($val.sub) > 0}
				<div class="submenu">
{foreach from=$val.sub key=txt item=url}
					<a href="{$url}">{$txt}</a><br />
{/foreach}
				</div>
{/if}
{/foreach}
				<div class="menu">
					<form method="post" action="?module=issues&action=view" title="View Issue">
						<input type="text" size="6" name="issueid" /><input type="submit" value="Go" />
					</form>
				</div>
			</fieldset>
		</div>
		<div id="content">
<!-- End leftnav.tpl -->

