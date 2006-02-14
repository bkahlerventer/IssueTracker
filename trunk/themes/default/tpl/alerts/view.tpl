<!-- Begin alerts/view.tpl -->
{opentable}
{titlebar colspan=2 title=$alert.title}
<tr><td class="subtitle">Posted {$alert.posted|userdate:TRUE} by {username id=$alert.userid}</td></tr>
<tr class="data">
<td>
{$alert.message|format}
</td>
</tr>
{closetable}
<!-- End alerts/view.tpl -->

