<?php 

$modul="mail_group";

require("inc/req.php");

// Generally for people with the right to edit mail_group
$groupID = 1024;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'mail_group/mail_group.php')) {
    require MODULE_ROOT.'mail_group/mail_group.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/


// Name
validate('name', 'string' );

// Mail_letter_id
validate('mail_letter_id', 'int nullable' );

// Sender
validate('sender', 'int' );

// Sql_filter
validate('sql_filter', 'string nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `mail_group` WHERE mail_group_id = " . (int) $_REQUEST['mail_group_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'mail_group/mail_group.delete.php')) {
        require MODULE_ROOT.'mail_group/mail_group.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['mail_group_id']) {
            $listUpdateSql = "UPDATE `mail_group` SET `" . $key . "`=" . $value . " WHERE mail_group_id = " . (int) $_REQUEST['mail_group_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `mail_group` WHERE mail_group_id = " . (int) $_REQUEST['mail_group_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `mail_group` SET name = "
    .$_VALIDDB['name']
     . ",mail_letter_id = " . $_VALIDDB['mail_letter_id']
     . ",sender = " . $_VALIDDB['sender']
     . ",sql_filter = " . $_VALIDDB['sql_filter']
    . " WHERE mail_group_id = " . (int) $_REQUEST['mail_group_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after mail_group update ***/
    
    } else {
        /*** before mail_group insert ***/
	    $sql = "INSERT INTO `mail_group`(name, mail_letter_id, sender, sql_filter) VALUES("
    .$_VALIDDB['name']
    . ",
	" . $_VALIDDB['mail_letter_id']
    . ",
	" . $_VALIDDB['sender']
    . ",
	" . $_VALIDDB['sql_filter']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['mail_group_id'] = mysqli_insert_id($con);
        /*** after mail_group insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'mail_group_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'mail_group_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'mail_group_id' => $_VALID['next_id']];
    } else {
        $loc = 'mail_group.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['mail_group_id']) {
	$sql = "SELECT * FROM `mail_group` WHERE mail_group_id = " . (int) $_REQUEST['mail_group_id'];
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
$n4a['mail_group.php'] = ss('Back to List');
$n4a['mail.php?prepare&mail_group_id=' . (int) $_REQUEST['mail_group_id']] = ss('Prepare Mails');
$n4a['mail.php?prepare&combine_pdf&mail_group_id=' . (int) $_REQUEST['mail_group_id']] = ss('Combined PDF');
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
            <li><span><?php echo ss('Mail_group')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formmail_group" name="formmail_group" method="post" class="formLayout" >
<?php if($_REQUEST['mail_group_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'mail_group.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['mail_group_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;mail_group_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;mail_group_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Mail_group')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="name-form-group" class="form-group <?php echo (isset($error['name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="name">
	<?php echo ss('Name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="name" id="name" value="<?php echo ss($_VALID['name'])?>" required="required" />
    </div>
<?php if (isset($error['name'])){ echo '<span class="help-block text-danger">'; echo $error['name'] . ''; echo '</span>';}?>
</div>
        
<div id="mail_letter_id-form-group" class="form-group <?php echo (isset($error['mail_letter_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="mail_letter_id">
	<?php echo ss('Mail_letter_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="mail_letter_id" id="mail_letter_id" /><?php echo basicConvert("mail_letter", $_VALID['mail_letter_id'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['mail_letter_id'])){ echo '<span class="help-block text-danger">'; echo $error['mail_letter_id'] . ''; echo '</span>';}?>
</div>
        
<div id="sender-form-group" class="form-group <?php echo (isset($error['sender']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="sender">
	<?php echo ss('Sender')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="sender" id="sender" required="required" /><?php echo basicConvert("mail_sender", $_VALID['sender'], 1, "from_mail", null, $groupID)?> </select />
    </div>
<?php if (isset($error['sender'])){ echo '<span class="help-block text-danger">'; echo $error['sender'] . ''; echo '</span>';}?>
</div>
        
<div id="sql_filter-form-group" class="form-group <?php echo (isset($error['sql_filter']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="sql_filter">
	<?php echo ss('Sql_filter')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="sql_filter" id="sql_filter"><?php echo ss($_VALID['sql_filter'])?></textarea>
    </div>
<?php if (isset($error['sql_filter'])){ echo '<span class="help-block text-danger">'; echo $error['sql_filter'] . ''; echo '</span>';}?>
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
<!-- after mail_group detail form -->
<?php
require("inc/footer.inc.php");