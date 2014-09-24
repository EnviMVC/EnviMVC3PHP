<%if $type == 'textarea'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$smarty.post.$name|nl2br%>
  </div>
<%elseif $type == 'date'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$smarty.post.$name%>
  </div>

<%elseif $type == 'time'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$smarty.post.$name%>
  </div>

<%elseif $type == 'datetime'%>
  <%assign var="datetime_date_key" value="`$name`_date"%>
  <%assign var="datetime_time_key" value="`$name`_time"%>
  <input type="hidden" name="<%$name%>_date" value="<%$smarty.post.$datetime_date_key%>">
  <input type="hidden" name="<%$name%>_time" value="<%$smarty.post.$datetime_time_key%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$smarty.post.$datetime_date_key%>
    <%$smarty.post.$datetime_time_key%>
  </div>

<%elseif $type == 'select'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%assign var="option_val" value=$smarty.post.$name%>
    <%$options[$option_val]%>
  </div>
<%elseif $type == 'radio'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%assign var="option_val" value=$smarty.post.$name%>
    <%$options[$option_val]%>
  </div>
<%elseif $type == 'checkbox'%>
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%foreach from=$smarty.post.$name key=key item=item%>
      <input type="hidden" name="<%$name%>[]" value="<%$item%>">
      <%$options[$item]%>
    <%/foreach%>
  </div>
<%elseif $type == 'flag'%>
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%if $smarty.post.$name == 1%>
    <%$options.1|default:"はい"%>
    <input type="hidden" name="<%$name%>" value="1">
    <%else%>
    <%$options.0|default:"いいえ"%>
    <%/if%>
  </div>
<%elseif $type == 'zip_code'%>
  <%assign var="zip_code_f_key" value="`$name`_f"%>
  <%assign var="zip_code_a_key" value="`$name`_a"%>
  <input type="hidden" name="<%$name%>_f" value="<%$smarty.post.$zip_code_f_key%>">
  <input type="hidden" name="<%$name%>_a" value="<%$smarty.post.$zip_code_a_key%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$default%>
  </div>
<%elseif $type == 'password'%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    *********************(セキュリティのため表示しません)
  </div>
<%else%>
  <input type="hidden" name="<%$name%>" value="<%$smarty.post.$name%>">
  <div class="form-group">
    <label for="form_<%$name%>"><%$form_name%></label>
    <%$smarty.post.$name%>
  </div>
<%/if%>

