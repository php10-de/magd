<?php 
    
$modul="hrose_cron";

require("inc/req.php");

// For Administrators only
GRGR(3);
//Form Hook After Group
/*** Validation ***/

// Hrose_cron_id
validate('hrose_cron_id', 'int nullable' );
$_SESSION[$modul]['hrose_cron_id'] = $_VALID[' hrose_cron_id'];

// Task
validate('task', 'string' );
$_SESSION[$modul]['task'] = $_VALID[' task'];

// Active
validate('active', 'ckb' );
$_SESSION[$modul]['active'] = $_VALID[' active'];

// Mhdmd
validate('mhdmd', 'string' );
$_SESSION[$modul]['mhdmd'] = $_VALID[' mhdmd'];

// File
validate('file', 'string' );
$_SESSION[$modul]['file'] = $_VALID[' file'];

// Parameters
validate('parameters', 'string nullable' );
$_SESSION[$modul]['parameters'] = $_VALID[' parameters'];

// Ran_at
validate('ran_at', 'int' );
$_SESSION[$modul]['ran_at'] = $_VALID[' ran_at'];

// End_time
validate('end_time', 'int nullable' );
$_SESSION[$modul]['end_time'] = $_VALID[' end_time'];

// Ok
validate('ok', 'int' );
$_SESSION[$modul]['ok'] = $_VALID[' ok'];

// Log_level
validate('log_level', 'ckb' );
$_SESSION[$modul]['log_level'] = $_VALID[' log_level'];
if (isset($_REQUEST['submitted']) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM hrose_cron WHERE hrose_cron_id = " . (int) $_REQUEST['hrose_cron_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	$sql = "UPDATE hrose_cron SET task = "
    .$_VALIDDB['task']
     . ",active = " . $_VALIDDB['active']
     . ",mhdmd = " . $_VALIDDB['mhdmd']
     . ",file = " . $_VALIDDB['file']
     . ",parameters = " . $_VALIDDB['parameters']
     . ",ran_at = " . $_VALIDDB['ran_at']
     . ",end_time = " . $_VALIDDB['end_time']
     . ",ok = " . $_VALIDDB['ok']
     . ",log_level = " . $_VALIDDB['log_level']
    . " WHERE hrose_cron_id = " . (int) $_REQUEST['hrose_cron_id'];
    /*** after hrose_cron update ***/
    mysqli_query($con, $sql) or die('DB Update Error');
    
    } else {
    
	$sql = "INSERT INTO hrose_cron(hrose_cron_id, task, active, mhdmd, file, parameters, ran_at, end_time, ok, log_level) VALUES("
    .$_VALIDDB['hrose_cron_id']
    . ",
" . $_VALIDDB['task']
    . ",
" . $_VALIDDB['active']
    . ",
" . $_VALIDDB['mhdmd']
    . ",
" . $_VALIDDB['file']
    . ",
" . $_VALIDDB['parameters']
    . ",
" . $_VALIDDB['ran_at']
    . ",
" . $_VALIDDB['end_time']
    . ",
" . $_VALIDDB['ok']
    . ",
" . $_VALIDDB['log_level']
    . ") ";
    mysqli_query($con, $sql) or die('DB Insert Error');
    /*** after hrose_cron insert ***/
    }
    header('Location: hrose_cron.php?ok=Done');
    exit;
}

if ($_REQUEST['hrose_cron_id']) {
	$sql = "SELECT * FROM hrose_cron WHERE hrose_cron_id = " . (int) $_REQUEST['hrose_cron_id'];
	$data = mysqli_fetch_assoc(mysqli_query($con, $sql));
    foreach ($data as $key => $value) {
        $_VALID[$key] = $value;
    }
}
// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $_VALID[$key] = $value;
    }
}
$n4a['hrose_cron.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?>
<a href="javascript:void(0)" onClick="window.location.href = 'hrose_cron.php'"><i class="fa  fa-align-justify listmenuicon" title="<?php sss('Back to List')?>"></i></a><br><br>

<div class="contentheadline"><?php echo ss('Hrose_cron')?></div>
<br>
<div class="contenttext">
<form id="formhrose_cron" name="formhrose_cron" method="post" class="formLayout">
<?php if($id) {
  echo '<input type="hidden" name="id" value="'.$id.'">';
}?>

<label for="task"><?php echo ss('Task')?></label>
<input type="text" name="task" id="task" value="<?php echo ss($_VALID['task'])?>" required="required" />
<?php if ($error['task']) echo $error['task'] . ''?>
<br>
<label for="active"><?php echo ss('Active')?></label>
<input type="checkbox" name="active" id="active" value="1" <?php echo ($_VALID['active'])?'checked="checked"':''?> required="required" />
<?php if ($error['active']) echo $error['active'] . ''?>
<br>
<label for="mhdmd"><?php echo ss('Mhdmd')?></label>
<input type="text" name="mhdmd" id="mhdmd" value="<?php echo ss($_VALID['mhdmd'])?>" required="required" />
<?php if ($error['mhdmd']) echo $error['mhdmd'] . ''?>
<br>
<label for="file"><?php echo ss('File')?></label>
<input type="text" name="file" id="file" value="<?php echo ss($_VALID['file'])?>" required="required" />
<?php if ($error['file']) echo $error['file'] . ''?>
<br>
<label for="parameters"><?php echo ss('Parameters')?></label>
<textarea name="parameters" id="parameters"><?php echo ss($_VALID['parameters'])?></textarea>
<?php if ($error['parameters']) echo $error['parameters'] . ''?>
<br>
<label for="ran_at"><?php echo ss('Ran_at')?></label>
<input type="text" name="ran_at" id="ran_at" value="<?php echo $_VALID['ran_at']?>" required="required" />
<?php if ($error['ran_at']) echo $error['ran_at'] . ''?>
<br>
<label for="end_time"><?php echo ss('End_time')?></label>
<input type="text" name="end_time" id="end_time" value="<?php echo $_VALID['end_time']?>" />
<?php if ($error['end_time']) echo $error['end_time'] . ''?>
<br>
<label for="ok"><?php echo ss('Ok')?></label>
<input type="text" name="ok" id="ok" value="<?php echo $_VALID['ok']?>" required="required" />
<?php if ($error['ok']) echo $error['ok'] . ''?>
<br>
<label for="log_level"><?php echo ss('Log_level')?></label>
<input type="checkbox" name="log_level" id="log_level" value="1" <?php echo ($_VALID['log_level'])?'checked="checked"':''?> required="required" />
<?php if ($error['log_level']) echo $error['log_level'] . ''?>
<br>
<input type="hidden" name="submitted" value="submitted">
<input type="submit" id="submit" value="<?php echo ss('Submit')?>">
<!-- After submit -->
</form>
<!-- after hrose_cron detail form -->
</div>
<?php
require("inc/footer.inc.php"); 
    ?>