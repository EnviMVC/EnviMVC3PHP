<%include file="_header.tpl"%>
<%include file="_error.tpl"%>
<ul>
  <%foreach from=$lists item=item key=key%>
    <li><a href="./show.php?id=<%$item.id%>"><%$item.id%></a></li>
  <%/foreach%>
</ul>


<%include file="_footer.tpl"%>