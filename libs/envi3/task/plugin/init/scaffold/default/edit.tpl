<%include file="_header.tpl"%>
<%include file="_error.tpl"%>
<form action="./update.php" method="post">
<input type="hidden" name="id" value="<%$smarty.get.id|default:$smarty.post.id%>">

/*%%form_update_text%%*/


<button type="submit" name="confirm" value="確認">確認</button>
</form>

<%include file="_footer.tpl"%>
