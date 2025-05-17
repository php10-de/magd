<!--
<h2 class="headline" onclick="toggleDiv('function');document.getElementById('is_function').value='1'">Use Function</h2>
<input type="hidden" id="is_function" name="is_function" value="<?php echo (int) $_REQUEST['is_function']?>">
<div id="function" style="display:<?php echo ($_REQUEST['is_function'])?'block':'none'?>">
<label><input type="checkbox" name="trim"  <?php echo ($_REQUEST['trim'])?' checked':''?> /> trim<br>
<label><input type="checkbox" name="custom_method" <?php echo ($_REQUEST['custom_method'] OR $_REQUEST['custom_method_name'])?' checked':''?>/> custom method </label><input type="text" name="custom_method_name" style="width:270px" value="<?php echo htmlspecialchars($_REQUEST['custom_method_name'])?>" /><br />
<label><input type="checkbox" name="arbitrary_before" <?php echo ($_REQUEST['arbitrary_before'] OR $_REQUEST['arbitrary_before_value'])?' checked':''?>/> arbitrary before </label><input type="text" name="arbitrary_before_value" style="width:270px" value="<?php echo htmlspecialchars(stripslashes($_REQUEST['arbitrary_before_value']))?>" /><br>
<label><input type="checkbox" name="arbitrary_after" <?php echo ($_REQUEST['arbitrary_after'] OR $_REQUEST['arbitrary_after_value'])?' checked':''?>/> arbitrary after </label><input type="text" name="arbitrary_after_value" style="width:270px" value="<?php echo htmlspecialchars(stripslashes($_REQUEST['arbitrary_after_value']))?>" /><br>
  </div>-->