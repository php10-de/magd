<?php 
    
$modul="cron";

require("inc/req.php");

// Generally for people with the right to edit cron
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'cron/cron.php')) {
    require MODULE_ROOT.'cron/cron.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Cron_id
validate('cron_id', 'int nullable' );

// Task
validate('task', 'string' );

// Active
validate('active', 'ckb' );

// Mhdmd
validate('mhdmd', 'string' );

// File
validate('file', 'string' );

// Parameters
validate('parameters', 'string nullable' );

// Ran_at
validate('ran_at', 'int nullable' );

// End_time
validate('end_time', 'int nullable' );

// Ok
validate('ok', 'ckb' );

// Log_level
validate('log_level', 'ckb nullable' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM cron WHERE cron_id = " . (int) $_REQUEST['cron_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE cron SET task = "
    .$_VALIDDB['task']
     . ",active = " . $_VALIDDB['active']
     . ",mhdmd = " . $_VALIDDB['mhdmd']
     . ",file = " . $_VALIDDB['file']
     . ",parameters = " . $_VALIDDB['parameters']
     . ",ran_at = " . $_VALIDDB['ran_at']
     . ",end_time = " . $_VALIDDB['end_time']
     . ",ok = " . $_VALIDDB['ok']
     . ",log_level = " . $_VALIDDB['log_level']
    . " WHERE cron_id = " . (int) $_REQUEST['cron_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after cron update ***/
    
    } else {
    
	    $sql = "INSERT INTO cron(cron_id, task, active, mhdmd, file, parameters, ran_at, end_time, ok, log_level) VALUES("
    .$_VALIDDB['cron_id']
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
        $_VALID['cron_id'] = mysqli_insert_id($con);
        /*** after cron insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'cron_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'cron_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'cron_id' => $_VALID['next_id']];
    } else {
        $loc = 'cron.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['cron_id']) {
	$sql = "SELECT * FROM cron WHERE cron_id = " . (int) $_REQUEST['cron_id'];
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
$n4a['cron.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?><header class="page-header">
    <!--    <h2>Title</h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('Cron')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left hidden"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formcron" name="formcron" method="post" class="formLayout" >
<?php if($_REQUEST['cron_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'cron.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['cron_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;cron_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;cron_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Cron')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['task']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="task"><?php echo ss('Task')?></label><div class="col-md-6">
<input class="form-control" type="text" name="task" id="task" value="<?php echo ss($_VALID['task'])?>" required="required" /></div>
<?php if (isset($error['task'])){ echo '<span class="help-block text-danger">'; echo $error['task'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['active']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="active"><?php echo ss('Active')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                            <input type="checkbox" name="active" id="active" value="1" <?php echo ($_VALID['active'])?'checked="checked"':''?> /><label for="active"></label></div></div>
<?php if (isset($error['active'])){ echo '<span class="help-block text-danger">'; echo $error['active'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['mhdmd']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="mhdmd"><?php echo ss('Mhdmd')?></label><div class="col-md-6">
<input class="form-control" type="text" name="mhdmd" id="mhdmd" value="<?php echo ss($_VALID['mhdmd'])?>" required="required" /></div>
<?php if (isset($error['mhdmd'])){ echo '<span class="help-block text-danger">'; echo $error['mhdmd'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['file']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="file"><?php echo ss('File')?></label><div class="col-md-6">
<input class="form-control" type="text" name="file" id="file" value="<?php echo ss($_VALID['file'])?>" required="required" /></div>
<?php if (isset($error['file'])){ echo '<span class="help-block text-danger">'; echo $error['file'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['parameters']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="parameters"><?php echo ss('Parameters')?></label><div class="col-md-6">
<textarea class="form-control" name="parameters" id="parameters"><?php echo ss($_VALID['parameters'])?></textarea></div>
<?php if (isset($error['parameters'])){ echo '<span class="help-block text-danger">'; echo $error['parameters'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['ran_at']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="ran_at"><?php echo ss('Ran_at')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                    <input type="text" class="form-control" name="ran_at" id="ran_at" value="<?php echo $_VALID['ran_at']?>" /><label for="ran_at"></label></div></div>
<?php if (isset($error['ran_at'])){ echo '<span class="help-block text-danger">'; echo $error['ran_at'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['end_time']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="end_time"><?php echo ss('End_time')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                    <input type="text" class="form-control" name="end_time" id="end_time" value="<?php echo $_VALID['end_time']?>" /><label for="end_time"></label></div></div>
<?php if (isset($error['end_time'])){ echo '<span class="help-block text-danger">'; echo $error['end_time'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['ok']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="ok"><?php echo ss('Ok')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                            <input type="checkbox" name="ok" id="ok" value="1" <?php echo ($_VALID['ok'])?'checked="checked"':''?> /><label for="ok"></label></div></div>
<?php if (isset($error['ok'])){ echo '<span class="help-block text-danger">'; echo $error['ok'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['log_level']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="log_level"><?php echo ss('Log_level')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                            <input type="checkbox" name="log_level" id="log_level" value="1" <?php echo ($_VALID['log_level'])?'checked="checked"':''?> /><label for="log_level"></label></div></div>
<?php if (isset($error['log_level'])){ echo '<span class="help-block text-danger">'; echo $error['log_level'] . ''; echo '</span>';}?></div>
                <input type="hidden" name="submitted" value="submitted">
                <input type="hidden" name="i" value="<?php echo $_VALID['i']+1?>">
                <input type="hidden" name="next_id" value="<?php echo $nextEntry[$modul.'_id']?>">
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">                        
                            <input type="submit" class="btn btn-primary"  id="submit" value="<?php echo ss('Submit')?>"/>
                            <input type="submit" class="btn btn-default" id="submit_new" name="submit_new" value="<?php echo ss('Submit & New')?>"/>
                            <input type="submit" class="btn btn-success" id="submit_next" name="submit_next" value="<?php echo ss('Submit & Next')?>" />
                        </div>
                    </div>
                </footer>
            </form>
            
        </section>
    </div>
</div>
<!-- after cron detail form -->
<?php
require("inc/footer.inc.php"); 
    ?>