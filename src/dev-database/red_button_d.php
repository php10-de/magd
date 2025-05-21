<?php 

$modul="red_button";

require("inc/req.php");

// Generally for people with the right to edit red_button
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'red_button/red_button.php')) {
    require MODULE_ROOT.'red_button/red_button.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Tablename
validate('tablename', 'string' );

// Replace_from
validate('replace_from', 'string nullable' );

// Replace_to
validate('replace_to', 'string nullable' );

// Patch
validate('patch', 'string nullable' );

// Is_active
validate('is_active', 'ckb nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `red_button` WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'red_button/red_button.delete.php')) {
        require MODULE_ROOT.'red_button/red_button.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['red_button_id']) {
            $listUpdateSql = "UPDATE `red_button` SET `" . $key . "`=" . $value . " WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `red_button` WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `red_button` SET tablename = "
    .$_VALIDDB['tablename']
     . ",replace_from = " . "'" . my_sql(lb($_VALID['replace_from'])) . "'"
     . ",replace_to = " . "'" . my_sql(lb($_VALID['replace_to'])) . "'"
     . ",patch = " . "'" . my_sql(lb($_VALID['patch'])) . "'"
     . ",is_active = " . $_VALIDDB['is_active']
    . " WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after red_button update ***/
    
    } else {
        /*** before red_button insert ***/
	    $sql = "INSERT INTO `red_button`(tablename, replace_from, replace_to, patch, is_active) VALUES("
    .$_VALIDDB['tablename']
    . ",
	" . "'" . my_sql(lb($_VALID['replace_from'])) . "'"
    . ",
	" . "'" . my_sql(lb($_VALID['replace_to'])) . "'"
    . ",
	" . "'" . my_sql(lb($_VALID['patch'])) . "'"
    . ",
	" . $_VALIDDB['is_active']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['red_button_id'] = mysqli_insert_id($con);
        /*** after red_button insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'red_button_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'red_button_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'red_button_id' => $_VALID['next_id']];
    } else {
        $loc = 'red_button.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['red_button_id']) {
	$sql = "SELECT * FROM `red_button` WHERE red_button_id = " . (int) $_REQUEST['red_button_id'];
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
$n4a['red_button.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Red_button')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formred_button" name="formred_button" method="post" class="formLayout" >
<?php if($_REQUEST['red_button_id']) {
  /** HTML Update-Form **/
    if (isset($_SESSION['ace']['saved'])) {
        if (isset($_SESSION['ace'][$modul]['replace_from'])) {
            $_VALID['replace_from'] = $_SESSION['ace'][$modul]['replace_from'];
        }
        if (isset($_SESSION['ace'][$modul]['replace_to'])) {
            $_VALID['replace_to'] = $_SESSION['ace'][$modul]['replace_to'];
        }
        if (isset($_SESSION['ace'][$modul]['patch'])) {
            $_VALID['patch'] = $_SESSION['ace'][$modul]['patch'];
        }
        unset($_SESSION['ace']);
    }
    $_SESSION[$modul]['data']['replace_from'] = $_VALID['replace_from'];
    $_SESSION[$modul]['data']['replace_to'] = $_VALID['replace_to'];
    $_SESSION[$modul]['data']['patch'] = $_VALID['patch'];
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'red_button.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['red_button_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;red_button_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;red_button_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Red_button')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="tablename-form-group" class="form-group <?php echo (isset($error['tablename']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="tablename">
	<?php echo ss('Tablename')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="tablename" id="tablename" value="<?php echo ss($_VALID['tablename'])?>" required="required" />
    </div>
<?php if (isset($error['tablename'])){ echo '<span class="help-block text-danger">'; echo $error['tablename'] . ''; echo '</span>';}?>
</div>
        
<div id="replace_from-form-group" class="form-group <?php echo (isset($error['replace_from']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="replace_from">
	<?php echo ss('Replace_from')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="replace_from" id="replace_from"><?php echo ss($_VALID['replace_from'])?></textarea>
    <a href="<?php echo $_SERVER['PHP_SELF']?>?ace&modul=red_button&field=replace_from&referer=<?php echo urlencode($_SERVER['REQUEST_URI'])?>&language=php" title="<?php sss('Edit Source')?>"><i class="fa fa-pencil"></i></a>
    </div>
<?php if (isset($error['replace_from'])){ echo '<span class="help-block text-danger">'; echo $error['replace_from'] . ''; echo '</span>';}?>
</div>
        
<div id="replace_to-form-group" class="form-group <?php echo (isset($error['replace_to']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="replace_to">
	<?php echo ss('Replace_to')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="replace_to" id="replace_to"><?php echo ss($_VALID['replace_to'])?></textarea>
    <a href="<?php echo $_SERVER['PHP_SELF']?>?ace&modul=red_button&field=replace_to&referer=<?php echo urlencode($_SERVER['REQUEST_URI'])?>&language=php" title="<?php sss('Edit Source')?>"><i class="fa fa-pencil"></i></a>
    </div>
<?php if (isset($error['replace_to'])){ echo '<span class="help-block text-danger">'; echo $error['replace_to'] . ''; echo '</span>';}?>
</div>
        
<div id="patch-form-group" class="form-group <?php echo (isset($error['patch']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="patch">
	<?php echo ss('Patch')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="patch" id="patch"><?php echo ss($_VALID['patch'])?></textarea>
    <a href="<?php echo $_SERVER['PHP_SELF']?>?ace&modul=red_button&field=patch&referer=<?php echo urlencode($_SERVER['REQUEST_URI'])?>&language=php" title="<?php sss('Edit Source')?>"><i class="fa fa-pencil"></i></a>
    </div>
<?php if (isset($error['patch'])){ echo '<span class="help-block text-danger">'; echo $error['patch'] . ''; echo '</span>';}?>
</div>
        
<div id="is_active-form-group" class="form-group <?php echo (isset($error['is_active']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="is_active">
	<?php echo ss('Is_active')?>
	</label>
    <div class="col-md-6">

        <div class="checkbox-custom checkbox-default">
            <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($_VALID['is_active'])?'checked="checked"':''?> />
            <label for="is_active"></label>
        </div>
    </div>
<?php if (isset($error['is_active'])){ echo '<span class="help-block text-danger">'; echo $error['is_active'] . ''; echo '</span>';}?>
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
<!-- after red_button detail form -->
<?php
require("inc/footer.inc.php");