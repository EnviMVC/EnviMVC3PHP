<%if $error && $error.count%>
<div class="alert alert-warning fade in" role="alert">
  <button type="button" class="close" data-dismiss="alert">
  <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="alert-heading"><span class="glyphicon glyphicon-exclamation-sign"></span><%$error.count%>件のエラーが有ります</h4>
  <p>
  <%foreach from=$error.message key=key item=item%>
  <li><%$item%></li>
  <%/foreach%>

  </p>
</div>

<%/if%>
