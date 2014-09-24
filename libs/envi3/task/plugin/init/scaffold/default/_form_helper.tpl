
<%if $type == 'textarea'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <textarea type="<%$type%>" name="<%$name%>" class="form-control" id="form_<%$name%>" placeholder="<%$form_name%>"><%$smarty.post.$name|default:$default%></textarea>
  </div>
<%elseif $type == 'datetime'%>
  <%assign var="datetime_date_key" value="`$name`_date"%>
  <%assign var="datetime_time_key" value="`$name`_time"%>
  <%assign var="datetime_date_default" value=$default|substr:0:10%>
  <%assign var="datetime_time_default" value=$default|substr:-8:5%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <input type="date" name="<%$name%>_date" id="form_<%$name%>_date" value="<%$smarty.post.$datetime_date_key|default:$datetime_date_default%>">
  <input type="time" name="<%$name%>_time" id="form_<%$name%>_time" value="<%$smarty.post.$datetime_time_key|default:$datetime_time_default%>">
  </div>
<%elseif $type == 'date'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <input type="date" name="<%$name%>" id="form_<%$name%>" value="<%$smarty.post.$name|default:$default%>">
  </div>
<%elseif $type == 'time'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <input type="time" name="<%$name%>" id="form_<%$name%>" value="<%$smarty.post.$name|default:$default%>">
  </div>
<%elseif $type == 'select'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <select name="<%$name%>" id="form_<%$name%>">
    <%html_options selected=$smarty.post.$name|default:$default options=$options%>
  </select>
  </div>
<%elseif $type == 'radio'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <%html_radios name=$name selected=$smarty.post.$name|default:$default options=$options%>
  </div>
<%elseif $type == 'checkbox'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <%html_checkboxes name="`$name`[]" selected=$smarty.post.$name|default:$default options=$options%>
  </div>
<%elseif $type == 'flag'%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <input type="checkbox" name="<%$name%>" value="1"<%if $default || $smarty.post.$name%> checked="checked"<%/if%>>
  </div>
<%elseif $type == 'zip_code'%>
  <%assign var="zip_code_f_key" value="`$name`_f"%>
  <%assign var="zip_code_a_key" value="`$name`_a"%>
  <%assign var="zip_code_f_default" value=$default|substr:0:3%>
  <%assign var="zip_code_a_default" value=$default|substr:-4:4%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <div class="row">
  <div class="col-xs-3">
  <input type="text" name="<%$name%>_f" class="form-control" id="form_<%$name%>" placeholder="<%$form_name_f%>" value="<%$smarty.post.$zip_code_f_key|default:$zip_code_f_default%>" size="3">
  </div>
  <div class="col-xs-1">
  -
  </div>
  <div class="col-xs-4">
  <input type="text" name="<%$name%>_a" class="form-control" id="form_<%$name%>" placeholder="<%$form_name_a%>" value="<%$smarty.post.$zip_code_a_key|default:$zip_code_a_default%>" size="4">
  </div>
  </div>
  </div>
<%else%>
  <div class="form-group">
  <label for="form_<%$name%>"><%$form_name%></label>
  <input type="<%$type%>" name="<%$name%>" class="form-control" id="form_<%$name%>" placeholder="<%$form_name%>" value="<%$smarty.post.$name|default:$default%>">
  </div>
<%/if%>
