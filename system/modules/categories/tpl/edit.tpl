<!-- Begin categories/edit.tpl -->
<form method="post" action="?module=categories&action=edit&id={$smarty.get.id}">
<input type="hidden" name="commit" value="true">
{opentable}
{titlebar colspan=2 title="Update Category"}
<tr>
<td width="20%" class="label" align="right" valign="top">Category:</td>
<td width="80%" class="data"><input type="text" size="32" name="category" value="{$category|stripslashes}"></td>
</tr>
<tr><td class="titlebar" colspan="2"><input type="submit" value="Update Category"></td></tr>
{closetable}
</form>
<!-- End categories/edit.tpl -->

