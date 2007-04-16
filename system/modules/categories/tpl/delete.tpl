<!-- Begin categories/delete.tpl -->
{opentable}
{titlebar colspan=2 title="Delete Category"}
<tr><td class="label" colspan="2">Are you sure you want to delete this category?</td></tr>
<tr>
<td class="data" align="center">
<form method="post" action="?module=categories&action=delete&id={$smarty.get.id}">
<input type="hidden" name="confirm" value="true" />
<input type="submit" value="Confirm" />
</form>
</td>
<td class="data" align="center">
<form method="post" action="?module=categories">
<input type="submit" value="Cancel" />
</form>
</td>
</tr>
{closetable}
<!-- End categories/delete.tpl -->

