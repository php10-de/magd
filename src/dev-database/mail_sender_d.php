<?php 

$modul="mail_sender";

require("inc/req.php");

// Generally for people with the right to edit mail_sender
$groupID = 1024;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'mail_sender/mail_sender.php')) {
    require MODULE_ROOT.'mail_sender/mail_sender.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// From_mail
validate('from_mail', 'string' );

// From_name
validate('from_name', 'string' );

// Response_mail
validate('response_mail', 'string' );

// Response_name
validate('response_name', 'string' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `mail_sender` WHERE mail_sender_id = " . (int) $_REQUEST['mail_sender_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'mail_sender/mail_sender.delete.php')) {
        require MODULE_ROOT.'mail_sender/mail_sender.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['mail_sender_id']) {
            $listUpdateSql = "UPDATE `mail_sender` SET `" . $key . "`=" . $value . " WHERE mail_sender_id = " . (int) $_REQUEST['mail_sender_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `mail_sender` WHERE mail_sender_id = " . (int) $_REQUEST['mail_sender_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `mail_sender` SET from_mail = "
    .$_VALIDDB['from_mail']
     . ",from_name = " . $_VALIDDB['from_name']
     . ",response_mail = " . $_VALIDDB['response_mail']
     . ",response_name = " . $_VALIDDB['response_name']
    . " WHERE mail_sender_id = " . (int) $_REQUEST['mail_sender_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after mail_sender update ***/
    
    } else {
        /*** before mail_sender insert ***/
	    $sql = "INSERT INTO `mail_sender`(from_mail, from_name, response_mail, response_name) VALUES("
    .$_VALIDDB['from_mail']
    . ",
	" . $_VALIDDB['from_name']
    . ",
	" . $_VALIDDB['response_mail']
    . ",
	" . $_VALIDDB['response_name']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['mail_sender_id'] = mysqli_insert_id($con);
        /*** after mail_sender insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'mail_sender_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'mail_sender_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'mail_sender_id' => $_VALID['next_id']];
    } else {
        $loc = 'mail_sender.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['mail_sender_id']) {
	$sql = "SELECT * FROM `mail_sender` WHERE mail_sender_id = " . (int) $_REQUEST['mail_sender_id'];
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
$n4a['mail_sender.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Mail_sender')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formmail_sender" name="formmail_sender" method="post" class="formLayout" >
<?php if($_REQUEST['mail_sender_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'mail_sender.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['mail_sender_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;mail_sender_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;mail_sender_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Mail_sender')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="from_mail-form-group" class="form-group <?php echo (isset($error['from_mail']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="from_mail">
	<?php echo ss('From_mail')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="from_mail" id="from_mail" value="<?php echo ss($_VALID['from_mail'])?>" required="required" />
    </div>
<?php if (isset($error['from_mail'])){ echo '<span class="help-block text-danger">'; echo $error['from_mail'] . ''; echo '</span>';}?>
</div>
        
<div id="from_name-form-group" class="form-group <?php echo (isset($error['from_name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="from_name">
	<?php echo ss('From_name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="from_name" id="from_name" value="<?php echo ss($_VALID['from_name'])?>" required="required" />
    </div>
<?php if (isset($error['from_name'])){ echo '<span class="help-block text-danger">'; echo $error['from_name'] . ''; echo '</span>';}?>
</div>
        
<div id="response_mail-form-group" class="form-group <?php echo (isset($error['response_mail']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="response_mail">
	<?php echo ss('Response_mail')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="response_mail" id="response_mail" value="<?php echo ss($_VALID['response_mail'])?>" required="required" />
    </div>
<?php if (isset($error['response_mail'])){ echo '<span class="help-block text-danger">'; echo $error['response_mail'] . ''; echo '</span>';}?>
</div>
        
<div id="response_name-form-group" class="form-group <?php echo (isset($error['response_name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="response_name">
	<?php echo ss('Response_name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="response_name" id="response_name" value="<?php echo ss($_VALID['response_name'])?>" required="required" />
    </div>
<?php if (isset($error['response_name'])){ echo '<span class="help-block text-danger">'; echo $error['response_name'] . ''; echo '</span>';}?>
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
<!-- after mail_sender detail form -->
<?php
require("inc/footer.inc.php");