<h1>list</h1>

<h2>{{$table->title}}</h2>
<table>
{{foreach from=$table->getDatas() key=key value=row}}
<tr>
{{foreach from=$row key=kk value=col}}
<td>
bbbbb
</td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
