<?php
$modul="settings";

require("inc/req.php");

validate("id","string");
validate("value","string");

/*** Rights ***/
// For Administrators only
GRGR(1);

if(isset($_REQUEST['submitted'])) {
    if ($_MISSING) {
        $headerError = ss('Some mandatory fields are missing');
    } else {
        $sql = "INSERT INTO setting(id, value, gr_id)
                VALUES (".strtoupper($_VALIDDB['id']).",".$_VALIDDB['value'].", 1)";
        $res = mysqli_query($con, $sql);
        $id = mysqli_insert_id($con);
        header('Location: setting.php?ok=Done');
    }
}

require("inc/header.inc.php");
?>
<a href="javascript:void(0)" onClick="window.location.href = 'setting.php'"><i class="fa  fa-align-justify listmenuicon" title="<?php sss('Back to List')?>"></i></a><br><br>
<div class="contentheadline"><?php sss('Settings')?></div>
<br>
<div class="contenttext">
  <form name="formsettings" class="formLayout">
  <?php if($_VALID['id']) {
      echo '<input type="hidden" name="id" value="'.$_VALID['id'].'">';
  }?>

<label for="id"><?php echo ss('ID')?></label>
<input type="text" name="id" id="id" value="<?php sss($data['id'])?>" required="required" />
<br>
<label for="value"><?php echo ss('Value')?></label>
<input type="text" name="value" id="value" value="<?php sss($data['value'])?>" required="required" />
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php sss('Submit')?>">
  </form>

<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}
?>
</div>
<?php
require("inc/footer.inc.php");
?>