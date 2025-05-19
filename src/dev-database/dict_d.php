<?php 

$modul="dict";

require("inc/req.php");

// Generally for people with the right to edit dict
$groupID = 5;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'dict/dict.php')) {
    require MODULE_ROOT.'dict/dict.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// ID
validate('ID', 'string' );

// Gr_id
validate('gr_id', 'int nullable' );

// De
validate('de', 'string nullable' );

// Cn
validate('cn', 'string nullable' );

// En
validate('en', 'string nullable' );

// Gr
validate('gr', 'string nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `dict` WHERE dict_id = " . (int) $_REQUEST['dict_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'dict/dict.delete.php')) {
        require MODULE_ROOT.'dict/dict.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['dict_id']) {
            $listUpdateSql = "UPDATE `dict` SET `" . $key . "`=" . $value . " WHERE dict_id = " . (int) $_REQUEST['dict_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `dict` WHERE dict_id = " . (int) $_REQUEST['dict_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `dict` SET ID = "
    .$_VALIDDB['ID']
     . ",gr_id = " . $_VALIDDB['gr_id']
     . ",de = " . $_VALIDDB['de']
     . ",cn = " . $_VALIDDB['cn']
     . ",en = " . $_VALIDDB['en']
     . ",gr = " . $_VALIDDB['gr']
    . " WHERE dict_id = " . (int) $_REQUEST['dict_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after dict update ***/
    
    } else {
        /*** before dict insert ***/
	    $sql = "INSERT INTO `dict`(ID, gr_id, de, cn, en, gr) VALUES("
    .$_VALIDDB['ID']
    . ",
	" . $_VALIDDB['gr_id']
    . ",
	" . $_VALIDDB['de']
    . ",
	" . $_VALIDDB['cn']
    . ",
	" . $_VALIDDB['en']
    . ",
	" . $_VALIDDB['gr']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['dict_id'] = mysqli_insert_id($con);
        /*** after dict insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'dict_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'dict_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'dict_id' => $_VALID['next_id']];
    } else {
        $loc = 'dict.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['dict_id']) {
	$sql = "SELECT * FROM `dict` WHERE dict_id = " . (int) $_REQUEST['dict_id'];
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
$n4a['dict.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Dict')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formdict" name="formdict" method="post" class="formLayout" >
<?php if($_REQUEST['dict_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'dict.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['dict_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;dict_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;dict_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Dict')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="ID-form-group" class="form-group <?php echo (isset($error['ID']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="ID">
	<?php echo ss('ID')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="ID" id="ID" value="<?php echo ss($_VALID['ID'])?>" required="required" />
    </div>
<?php if (isset($error['ID'])){ echo '<span class="help-block text-danger">'; echo $error['ID'] . ''; echo '</span>';}?>
</div>
        
<div id="gr_id-form-group" class="form-group <?php echo (isset($error['gr_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="gr_id">
	<?php echo ss('Gr_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="gr_id" id="gr_id" /><?php echo basicConvert("gr", $_VALID['gr_id'], 1, "shortname", null, $groupID)?> </select />
    </div>
<?php if (isset($error['gr_id'])){ echo '<span class="help-block text-danger">'; echo $error['gr_id'] . ''; echo '</span>';}?>
</div>
        
<div id="de-form-group" class="form-group <?php echo (isset($error['de']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="de">
	<?php echo ss('De')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="de" id="de" value="<?php echo ss($_VALID['de'])?>" />
    </div>
<?php if (isset($error['de'])){ echo '<span class="help-block text-danger">'; echo $error['de'] . ''; echo '</span>';}?>
</div>
        
<div id="cn-form-group" class="form-group <?php echo (isset($error['cn']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="cn">
	<?php echo ss('Cn')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="cn" id="cn" value="<?php echo ss($_VALID['cn'])?>" />
    </div>
<?php if (isset($error['cn'])){ echo '<span class="help-block text-danger">'; echo $error['cn'] . ''; echo '</span>';}?>
</div>
        
<div id="en-form-group" class="form-group <?php echo (isset($error['en']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="en">
	<?php echo ss('En')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="en" id="en" value="<?php echo ss($_VALID['en'])?>" />
    </div>
<?php if (isset($error['en'])){ echo '<span class="help-block text-danger">'; echo $error['en'] . ''; echo '</span>';}?>
</div>
        
<div id="gr-form-group" class="form-group <?php echo (isset($error['gr']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="gr">
	<?php echo ss('Gr')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="gr" id="gr" value="<?php echo ss($_VALID['gr'])?>" />
    </div>
<?php if (isset($error['gr'])){ echo '<span class="help-block text-danger">'; echo $error['gr'] . ''; echo '</span>';}?>
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
<!-- after dict detail form -->
<?php
require("inc/footer.inc.php");