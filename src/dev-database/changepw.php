<?php
$modul="changepw";
$area="all";

require("inc/req.php");

validate("pass_old","string");
validate("pass1","string");
validate("pass2","string");
$success = false;

if($_VALID['pass_old']!=""&&$_VALID['pass1']!=""&&$_VALID['pass2']!="") {

    if ($_VALID['pass1']==$_VALID['pass2']) {

        $membersql="UPDATE user
                        SET password = '" . my_sql(sha1($_VALID['pass1'].SALT)) . "'
                        WHERE password = '" . my_sql(sha1($_VALID['pass_old'].SALT)) . "'
                        AND user_id = '" . $_SESSION['user_id'] . "'";
        $memberresult = mysqli_query($con, $membersql);
        $resultB = mysql_affected_rows();
        if (!$resultB) {
            $headerError = ss('Wrong password.');
        } else {
            header('Location: start.php?ok=Passwort changed');
            $success = true;
        }

    } else {
        $headerError = ss('Passwords do not match.');
    }

} else if ($_REQUEST['submitted']) {
    $headerError = ss('Some fields are missing.');
}

require("inc/header.inc.php");
?>
<div class="contentheadline"><?php sss('Change Password')?></div>
<br>
<div class="contenttext">
<?php
if ($success) {
    echo ss("Password changed.");
} else {
    ?>
    <form name="changepw" method="post" class="formLayout">
      <label for="email"><?php echo ss('Old')?></label>
      <input type="password" name="pass_old" required="required"><br>
      <label for="pass1"><?php echo ss('New')?></label>
      <input type="password" name="pass1" required="required"><br>
      <label for="pass2"><?php echo ss('New (re-type)')?></label>
      <input type="password" name="pass2" required="required"><br>
      <input type="submit" name="submitted" class="sub-button" value="<?php sss('Submit')?>">
    </form>
    
    <?php if($err!="") {
        echo '<span class="red">'.$err.'</span>';
    } ?>

    <?php
}
require("inc/footer.inc.php");
?>
<script language="javascript">
jQuery(window).load(function() {	
		$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
		});
			jQuery('#loading-image').hide();
		});
$("#loading-image")
		.bind("ajaxSend", function(){
		$(this).show();
		})
		.bind("ajaxComplete", function(){
		$(this).hide();
		});

</script>


<style type="text/css" >
#loading-image {	
	width: 65px;
	height: 55px;
	position: fixed;
	right: 200px;
	z-index: 1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	margin-right:550px;
	top:100px;;
	-khtml-border-radius: 10px;
}
</style>
</div>