<%include file="_header.tpl"%>
<%include file="_error.tpl"%>
<form action="./update.php" method="post">
<input type="hidden" name="id" value="<%$smarty.get.id|default:$smarty.post.id%>">

/*%%confirm_text%%*/


<button type="submit" name="commit" value="送信">送信</button>
</form>

<%include file="_footer.tpl"%>