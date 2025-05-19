<?php 

$modul="augmented";

require("inc/req.php");

// Generally for people with the right to edit augmented
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'augmented/augmented.php')) {
    require MODULE_ROOT.'augmented/augmented.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Augmented_id
validate('augmented_id', 'int nullable' );

// Profile_id
validate('profile_id', 'int' );

// Site_url
validate('site_url', 'string' );

// Selector
validate('selector', 'string nullable' );

// Html
validate('html', 'string nullable' );

// Action
validate('action', 'string' );

// Active
validate('active', 'ckb' );

// Dupdate
validate('dupdate', 'datetime nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `augmented` WHERE augmented_id = " . (int) $_REQUEST['augmented_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'augmented/augmented.delete.php')) {
        require MODULE_ROOT.'augmented/augmented.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['augmented_id']) {
            $listUpdateSql = "UPDATE `augmented` SET `" . $key . "`=" . $value . " WHERE augmented_id = " . (int) $_REQUEST['augmented_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `augmented` WHERE augmented_id = " . (int) $_REQUEST['augmented_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `augmented` SET profile_id = "
    .$_VALIDDB['profile_id']
     . ",site_url = " . $_VALIDDB['site_url']
     . ",selector = " . $_VALIDDB['selector']
     . ",html = " . $_VALIDDB['html']
     . ",action = " . $_VALIDDB['action']
     . ",active = " . $_VALIDDB['active']
     . ",dupdate = " . $_VALIDDB['dupdate']
    . " WHERE augmented_id = " . (int) $_REQUEST['augmented_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after augmented update ***/
    
    } else {
        /*** before augmented insert ***/
	    $sql = "INSERT INTO `augmented`(augmented_id, profile_id, site_url, selector, html, action, active, dupdate) VALUES("
    .$_VALIDDB['augmented_id']
    . ",
	" . $_VALIDDB['profile_id']
    . ",
	" . $_VALIDDB['site_url']
    . ",
	" . $_VALIDDB['selector']
    . ",
	" . $_VALIDDB['html']
    . ",
	" . $_VALIDDB['action']
    . ",
	" . $_VALIDDB['active']
    . ",
	" . $_VALIDDB['dupdate']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['augmented_id'] = mysqli_insert_id($con);
        /*** after augmented insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'augmented_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'augmented_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'augmented_id' => $_VALID['next_id']];
    } else {
        $loc = 'augmented.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['augmented_id']) {
	$sql = "SELECT * FROM `augmented` WHERE augmented_id = " . (int) $_REQUEST['augmented_id'];
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
$n4a['augmented.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Augmented')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formaugmented" name="formaugmented" method="post" class="formLayout" >
<?php if($_REQUEST['augmented_id']) {
  /** HTML Update-Form **/
    $_SESSION[$modul]['data']['html'] = $_VALID['html'];
    if (isset($_SESSION['ace'][$modul]['html'])) {
        $_VALID['html'] = $_SESSION['ace'][$modul]['html'];
    }
    $_SESSION[$modul]['data']['action'] = $_VALID['action'];
    if (isset($_SESSION['ace'][$modul]['action'])) {
        $_VALID['action'] = $_SESSION['ace'][$modul]['action'];
    }
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'augmented.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['augmented_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;augmented_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;augmented_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Augmented')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="profile_id-form-group" class="form-group <?php echo (isset($error['profile_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="profile_id">
	<?php echo ss('Profile_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="profile_id" id="profile_id" required="required" /><?php echo basicConvert("profile", $_VALID['profile_id'], 1, "profile_name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['profile_id'])){ echo '<span class="help-block text-danger">'; echo $error['profile_id'] . ''; echo '</span>';}?>
</div>
        
<div id="site_url-form-group" class="form-group <?php echo (isset($error['site_url']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="site_url">
	<?php echo ss('Site_url')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="site_url" id="site_url" value="<?php echo ss($_VALID['site_url'])?>" required="required" />
    </div>
<?php if (isset($error['site_url'])){ echo '<span class="help-block text-danger">'; echo $error['site_url'] . ''; echo '</span>';}?>
</div>
        
<div id="selector-form-group" class="form-group <?php echo (isset($error['selector']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="selector">
	<?php echo ss('Selector')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="selector" id="selector" value="<?php echo ss($_VALID['selector'])?>" />
    </div>
<?php if (isset($error['selector'])){ echo '<span class="help-block text-danger">'; echo $error['selector'] . ''; echo '</span>';}?>
</div>
        
<div id="html-form-group" class="form-group <?php echo (isset($error['html']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="html">
	<?php echo ss('Html')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="html" id="html"><?php echo ss($_VALID['html'])?></textarea>
    <a href="<?php echo $_SERVER['PHP_SELF']?>?ace&modul=augmented&field=html&language=html&referer=<?php echo urlencode($_SERVER['REQUEST_URI'])?>" title="<?php sss('Edit Source')?>"><i class="fa fa-pencil"></i></a>
    </div>
<?php if (isset($error['html'])){ echo '<span class="help-block text-danger">'; echo $error['html'] . ''; echo '</span>';}?>
</div>
        
<div id="action-form-group" class="form-group <?php echo (isset($error['action']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="action">
	<?php echo ss('Action')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="action" id="action"><?php echo ss($_VALID['action'])?></textarea>
    <a href="<?php echo $_SERVER['PHP_SELF']?>?ace&modul=augmented&field=action&language=js&referer=<?php echo urlencode($_SERVER['REQUEST_URI'])?>" title="<?php sss('Edit Source')?>"><i class="fa fa-pencil"></i></a>
    </div>
<?php if (isset($error['action'])){ echo '<span class="help-block text-danger">'; echo $error['action'] . ''; echo '</span>';}?>
</div>
        
<div id="active-form-group" class="form-group <?php echo (isset($error['active']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="active">
	<?php echo ss('Active')?>
	</label>
    <div class="col-md-6">

        <div class="checkbox-custom checkbox-default">
            <input type="checkbox" name="active" id="active" value="1" <?php echo ($_VALID['active'])?'checked="checked"':''?> />
            <label for="active"></label>
        </div>
    </div>
<?php if (isset($error['active'])){ echo '<span class="help-block text-danger">'; echo $error['active'] . ''; echo '</span>';}?>
</div>
        
<div id="dupdate-form-group" class="form-group <?php echo (isset($error['dupdate']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="dupdate">
	<?php echo ss('Dupdate')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="dupdate" id="dupdate" value="<?php echo ss($_VALID['dupdate'])?>" />
    </div>
<?php if (isset($error['dupdate'])){ echo '<span class="help-block text-danger">'; echo $error['dupdate'] . ''; echo '</span>';}?>
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
<!-- after augmented detail form -->
<?php
require("inc/footer.inc.php");