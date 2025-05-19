<?php 

$modul="mail_opt";

require("inc/req.php");

// Generally for people with the right to edit mail_opt
$groupID = 1024;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'mail_opt/mail_opt.php')) {
    require MODULE_ROOT.'mail_opt/mail_opt.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Mail_opt_id
validate('mail_opt_id', 'int nullable' );

// Mail_group_id
validate('mail_group_id', 'int nullable' );

// Mail
validate('mail', 'string' );

// Inout
validate('inout', 'enum', array('in','out') );

// Opt_date
validate('opt_date', 'datetime' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `mail_opt` WHERE mail_opt_id = " . (int) $_REQUEST['mail_opt_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'mail_opt/mail_opt.delete.php')) {
        require MODULE_ROOT.'mail_opt/mail_opt.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['mail_opt_id']) {
            $listUpdateSql = "UPDATE `mail_opt` SET `" . $key . "`=" . $value . " WHERE mail_opt_id = " . (int) $_REQUEST['mail_opt_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `mail_opt` WHERE mail_opt_id = " . (int) $_REQUEST['mail_opt_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `mail_opt` SET mail_group_id = "
    .$_VALIDDB['mail_group_id']
     . ",mail = " . $_VALIDDB['mail']
     . ",inout = " . $_VALIDDB['inout']
     . ",opt_date = " . $_VALIDDB['opt_date']
    . " WHERE mail_opt_id = " . (int) $_REQUEST['mail_opt_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after mail_opt update ***/
    
    } else {
        /*** before mail_opt insert ***/
	    $sql = "INSERT INTO `mail_opt`(mail_opt_id, mail_group_id, mail, inout, opt_date) VALUES("
    .$_VALIDDB['mail_opt_id']
    . ",
	" . $_VALIDDB['mail_group_id']
    . ",
	" . $_VALIDDB['mail']
    . ",
	" . $_VALIDDB['inout']
    . ",
	" . $_VALIDDB['opt_date']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['mail_opt_id'] = mysqli_insert_id($con);
        /*** after mail_opt insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'mail_opt_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'mail_opt_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'mail_opt_id' => $_VALID['next_id']];
    } else {
        $loc = 'mail_opt.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['mail_opt_id']) {
	$sql = "SELECT * FROM `mail_opt` WHERE mail_opt_id = " . (int) $_REQUEST['mail_opt_id'];
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
$n4a['mail_opt.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Mail_opt')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formmail_opt" name="formmail_opt" method="post" class="formLayout" >
<?php if($_REQUEST['mail_opt_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'mail_opt.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['mail_opt_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;mail_opt_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;mail_opt_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Mail_opt')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="mail_group_id-form-group" class="form-group <?php echo (isset($error['mail_group_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="mail_group_id">
	<?php echo ss('Mail_group_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="mail_group_id" id="mail_group_id" /><?php echo basicConvert("mail_group", $_VALID['mail_group_id'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['mail_group_id'])){ echo '<span class="help-block text-danger">'; echo $error['mail_group_id'] . ''; echo '</span>';}?>
</div>
        
<div id="mail-form-group" class="form-group <?php echo (isset($error['mail']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="mail">
	<?php echo ss('Mail')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="mail" id="mail" value="<?php echo ss($_VALID['mail'])?>" required="required" />
    </div>
<?php if (isset($error['mail'])){ echo '<span class="help-block text-danger">'; echo $error['mail'] . ''; echo '</span>';}?>
</div>
        
<div id="inout-form-group" class="form-group <?php echo (isset($error['inout']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="inout">
	<?php echo ss('Inout')?>
	</label>
    <div class="col-md-6">

        <div class="radio-custom">

            <input type="radio"  required="required" name="inout" id="inout_in" value="in" <?php echo ($_VALID['inout'] == 'in')?'checked="checked"':''?> /><label name="inout" > <?php echo ss('in')?></label>
        </div>

        <div class="radio-custom">

            <input type="radio"  required="required" name="inout" id="inout_out" value="out" <?php echo ($_VALID['inout'] == 'out')?'checked="checked"':''?> /><label name="inout" > <?php echo ss('out')?></label>
        </div>

    </div>
<?php if (isset($error['inout'])){ echo '<span class="help-block text-danger">'; echo $error['inout'] . ''; echo '</span>';}?>
</div>
        
<div id="opt_date-form-group" class="form-group <?php echo (isset($error['opt_date']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="opt_date">
	<?php echo ss('Opt_date')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="opt_date" id="opt_date" value="<?php echo ss($_VALID['opt_date'])?>" required="required" />
    </div>
<?php if (isset($error['opt_date'])){ echo '<span class="help-block text-danger">'; echo $error['opt_date'] . ''; echo '</span>';}?>
</div>

                <input type="hidden" name="submitted" value="submitted">
                <input type="hidden" name="i" value="<?php echo $_VALID['i']+1?>">
                <input type="hidden" name="next_id" value="<?php echo $nextEntry[$modul.'_id']?>">
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">                        
                            <button type="submit" class="btn btn-success"  id="submit" value="<?php echo ss('Save')?>"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo ss('Save')?></button>
                            <button type="submit" class="btn btn-info" id="submit_new" name="submit_new" value="<?php echo ss('Save & New')?>"><i class="fa fa-plus"></i>&nbsp;<?php echo ss('Save & New')?></button>
                            <button type="submit" class="btn btn-primary" id="submit_next" name="submit_next" value="<?php echo ss('Save & Next')?>" ><i class="fa fa-arrow-right"></i>&nbsp;<?php echo ss('Save & Next')?></button>
                            <!-- after submit buttons -->
                        </div>
                    </div>
                </footer>
            </form>
            
        </section>
    </div>
</div>
<!-- after mail_opt detail form -->
<?php
require("inc/footer.inc.php");